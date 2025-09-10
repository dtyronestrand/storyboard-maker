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

        // It's a good practice to redirect to the page they were on, 
        // but for simplicity, we'll redirect to the dashboard.
        return Redirect::route('dashboard')->with('success', 'Successfully authorized with Google!');
    }

    /**
     * Export the storyboard to a Google Doc.
     */
    public function exportToGoogleDocs(Request $request, Client $client, Course $course)
    {
        if (!$request->session()->has('google_token')) {
            // Store the intended export URL in the session so we can redirect back to it after auth
            $request->session()->put('google_redirect_after_auth', route('storyboard.export', $course, false));
            return Redirect::route('google.redirect');
        }

        $client->setAccessToken($request->session()->get('google_token'));

        if ($client->isAccessTokenExpired()) {
             // If the token is expired, we need to refresh it.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $request->session()->put('google_token', $client->getAccessToken());
            } else {
                // If there's no refresh token, the user needs to re-authenticate.
                $request->session()->forget('google_token');
                 $request->session()->put('google_redirect_after_auth', route('storyboard.export', $course, false));
                return Redirect::route('google.redirect');
            }
        }

        $docs = new Docs($client);
        $course->load('modules.moduleItems');

        // Create a new document
        $document = new Docs\Document([
            'title' => $course->title . ' Storyboard'
        ]);
        $document = $docs->documents->create($document);

        // Build the content for the document
        $requests = $this->buildDocumentRequests($course);
        
        $batchUpdateRequest = new Docs\BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $docs->documents->batchUpdate($document->documentId, $batchUpdateRequest);
        
        // After exporting, you can clear the redirect session variable
        $request->session()->forget('google_redirect_after_auth');

        return Redirect::to('https://docs.google.com/document/d/' . $document->documentId . '/edit');
    }

    /**
     * Build the requests array for the Google Docs API.
     */
    private function buildDocumentRequests(Course $course): array
    {
        $requests = [];
        $currentIndex = 1;

        // Helper function to add text and update index
        $addText = function ($text, $style = 'NORMAL_TEXT') use (&$requests, &$currentIndex) {
            $requests[] = new Docs\Request([
                'insertText' => [
                    'location' => ['index' => $currentIndex],
                    'text' => $text,
                ]
            ]);

            if ($style !== 'NORMAL_TEXT') {
                 $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $currentIndex,
                            'endIndex' => $currentIndex + strlen($text),
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => $style,
                        ],
                        'fields' => 'namedStyleType',
                    ]
                ]);
            }
           
            $currentIndex += strlen($text);
        };

        // Course Title and Description
        $addText($course->title . "\n", 'TITLE');
        if ($course->description) {
            $addText($course->description . "\n\n");
        }

        // Modules and Module Items
        foreach ($course->modules as $module) {
            $addText($module->title . "\n", 'HEADING_1');

            foreach ($module->moduleItems as $item) {
                $addText($item->title . "\n", 'HEADING_2');
                if ($item->content) {
                    $addText(strip_tags($item->content) . "\n\n");
                }
            }
        }

        return $requests;
    }
}