<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Google\Client;
use Google\Service\Docs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StoryboardController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Client $client)
    {
        return Inertia::location($client->createAuthUrl());
    }

    /**
     * Handle the callback from Google after authentication.
     */
    public function handleGoogleCallback(Request $request, Client $client)
    {
        // Check if the user denied the request
        if ($request->has('error')) {
            return redirect('/dashboard')->with('error', 'You have cancelled the Google Docs authorization.');
        }
        
        $token = $client->fetchAccessTokenWithAuthCode($request->code);
        $request->session()->put('google_token', $token);

        return Redirect::route('dashboard')->with('success', 'Successfully authorized with Google!');
    }

    /**
     * Export the storyboard to a Google Doc.
     */
    public function exportToGoogleDocs(Request $request, Client $client, Course $course)
    {
        if (!$request->session()->has('google_token')) {
            $request->session()->put('google_redirect_after_auth', route('storyboard.export', $course, false));
            return Redirect::route('google.redirect');
        }

        $client->setAccessToken($request->session()->get('google_token'));

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $request->session()->put('google_token', $client->getAccessToken());
            } else {
                $request->session()->forget('google_token');
                $request->session()->put('google_redirect_after_auth', route('storyboard.export', $course, false));
                return Redirect::route('google.redirect');
            }
        }

        $docs = new Docs($client);
        $course->load('modules');

        // Create a new document
        $document = new Docs\Document([
            'title' => $course->prefix . " " . $course->number . " " .  ' Storyboard'
        ]);
        $document = $docs->documents->create($document);

        // Build the content for the document
        $requests = $this->buildDocumentRequests($course);
        
        $batchUpdateRequest = new Docs\BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $docs->documents->batchUpdate($document->documentId, $batchUpdateRequest);
        
        $request->session()->forget('google_redirect_after_auth');

        return Inertia::location('https://docs.google.com/document/d/' . $document->documentId . '/edit');
    }

    /**
     * Build the requests array for the Google Docs API.
     */
/**
     * Build the requests array for the Google Docs API.
     */
    private function buildDocumentRequests(Course $course): array
    {
    $requests = [];
        $currentIndex = 1;

        // Helper function to add text with optional styling
        $addText = function ($text, $style = 'NORMAL_TEXT') use (&$requests, &$currentIndex) {
            if ($text === null || $text === '') return; // Skip if text is null or empty
            
            $text = is_array($text) ? json_encode($text) : $text;
            $cleanedText = strip_tags((string)$text); // Ensure it's a string and strip HTML
            if ($cleanedText === '') return; // Skip if only HTML tags were present

            $textToInsert = $cleanedText . "\n"; // <-- THE FIX IS HERE: Always add a newline

            $requests[] = new Docs\Request([
                'insertText' => [
                    'location' => ['index' => $currentIndex],
                    'text' => $textToInsert,
                ]
            ]);

            if ($style !== 'NORMAL_TEXT') {
                 $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $currentIndex,
                            'endIndex' => $currentIndex + strlen($cleanedText), // Style only the text, not the newline
                        ],
                        'paragraphStyle' => [ 'namedStyleType' => $style ],
                        'fields' => 'namedStyleType',
                    ]
                ]);
            }
           
            $currentIndex += strlen($textToInsert); // Update index with the full length including newline
        };
        
        // Helper function to add a bolded label
        $addLabel = function($label) use (&$requests, &$currentIndex) {
            $textToInsert = $label . " "; // Add a space after the label
            $requests[] = new Docs\Request([
                'insertText' => [
                    'location' => ['index' => $currentIndex],
                    'text' => $textToInsert,
                ]
            ]);
            $requests[] = new Docs\Request([
                'updateTextStyle' => [
                    'range' => [
                        'startIndex' => $currentIndex,
                        'endIndex' => $currentIndex + strlen($label), // Only bold the label
                    ],
                    'textStyle' => [ 'bold' => true ],
                    'fields' => 'bold',
                ]
            ]);
            $currentIndex += strlen($textToInsert);
        };


        // 1. Course Title and Description
        $addText($course->prefix . " " . $course->number . "\n", 'TITLE');
        $addText($course->name . "\n\n", 'SUBTITLE');
        $addText("Course Objectives:\n", 'HEADING_1');
        if (!empty($course->objectives)) {
            foreach ($course->objectives as $obj) {
                $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                $index = array_search($obj, $course->objectives);
                $prefix = $index !== false && $index < count($romanNumerals) ? $romanNumerals[$index] . ". " : "- ";
        
                $addText($prefix . $obj . "\n");
            }
        } else {
            $addText("No objectives specified.\n");
        }
        $addText("\n");

        // 2. Modules and Module Items
        foreach ($course->modules as $module) {
            $addText("Module " . $module->number . $module->title . "\n", 'HEADING_1');

            if (empty($module->items)) {
                continue;
            }

            // 3. Loop through each item in the module
            foreach ($module->items as $item) {
                $type = $item['type'] ?? 'unknown';
                $data = $item['data'] ?? [];
                $title = $data['title'] ?? ucfirst($type); // Fallback title

                $addText($title . "\n", 'HEADING_2');

                // 4. Handle each item type with a switch statement
                switch ($type) {
                    case 'overview':
                    case 'page':
                    case 'wrap-up':
                        $addText(($data['content'] ?? 'No content.') . "\n\n");
                        break;
                    
                    case 'discussion':
                        $addLabel("Prompt: ");
                        $addText(($data['prompt'] ?? 'Not specified.') . "\n\n");
                        break;

                    case 'assignment':
                        $addLabel("Purpose: ");
                        $addText(($data['purpose'] ?? 'Not specified.') . "\n");
                        $addLabel("Criteria: ");
                        $addText(($data['criteria'] ?? 'Not specified.') . "\n\n");
                        break;

                    default:
                        // For any other type, just dump the data as JSON
                        $addText(json_encode($data, JSON_PRETTY_PRINT) . "\n\n");
                        break;
                }
            }
        }

        return $requests;
    }
}