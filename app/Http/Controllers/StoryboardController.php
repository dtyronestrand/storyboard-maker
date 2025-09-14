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

        $documentId = null;

        if ($course->document_id) {
            // Update existing document
            $documentId = $course->document_id;
            
            // Clear existing content
            $existingDoc = $docs->documents->get($documentId);
            $endIndex = $existingDoc->body->content[count($existingDoc->body->content) - 1]->endIndex - 1;
            
            $clearRequest = new Docs\BatchUpdateDocumentRequest([
                'requests' => [
                    new Docs\Request([
                        'deleteContentRange' => [
                            'range' => [
                                'startIndex' => 1,
                                'endIndex' => $endIndex
                            ]
                        ]
                    ])
                ]
            ]);
            $docs->documents->batchUpdate($documentId, $clearRequest);
        } else {
            // Create new document
            $document = new Docs\Document([
                'title' => $course->prefix . " " . $course->number . " " .  ' Storyboard'
            ]);
            $document = $docs->documents->create($document);
            $documentId = $document->documentId;
            
            // Save document ID to course
            $course->update(['document_id' => $documentId]);
        }

        // Build and insert content
        $requests = $this->buildDocumentRequests($course);
        
        $batchUpdateRequest = new Docs\BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $docs->documents->batchUpdate($documentId, $batchUpdateRequest);
        
        $request->session()->forget('google_redirect_after_auth');

        return Inertia::location('https://docs.google.com/document/d/' . $documentId . '/edit');
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
        $addText($course->name . "\n", 'SUBTITLE');
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
       

        // 2. Modules and Module Items
        foreach ($course->modules()->with('moduleItems.quizQuestions')->orderBy('number')->get() as $module) {
            $moduleTitle = "Module " . $module->number . " " . $module->title . "\n";
            $startIndex = $currentIndex;
            
            $requests[] = new Docs\Request([
                'insertText' => [
                    'location' => ['index' => $currentIndex],
                    'text' => $moduleTitle,
                ]
            ]);
            
            $requests[] = new Docs\Request([
                'updateParagraphStyle' => [
                    'range' => [
                        'startIndex' => $startIndex,
                        'endIndex' => $currentIndex + strlen($moduleTitle) - 1,
                    ],
                    'paragraphStyle' => [
                        'namedStyleType' => 'HEADING_1',
                        'shading' => [
                            'backgroundColor' => [
                                'color' => [
                                    'rgbColor' => [
                                        'red' => 164/255,
                                        'green' => 32/255,
                                        'blue' => 54/255
                                    ]
                                ]
                                    ]
                                    ],
                      
                    ],
                    'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor',
                ]
            ]);
            
            $requests[] = new Docs\Request([
                'updateTextStyle' => [
                    'range' => [
                        'startIndex' => $startIndex,
                        'endIndex' => $currentIndex + strlen($moduleTitle) - 1,
                    ],
                    'textStyle' => [
                        'foregroundColor' => [
                            'color' => [
                                'rgbColor' => [
                                    'red' => 1.0,
                                    'green' => 1.0,
                                    'blue' => 1.0
                                ]
                            ]
                        ]
                    ],
                    'fields' => 'foregroundColor.color.rgbColor',
                ]
            ]);
            
            $currentIndex += strlen($moduleTitle);

            if (empty($module->items)) {
                continue;
            }

            // 3. Loop through each item in the module
            $items = $module->moduleItems->isNotEmpty() ? $module->moduleItems : collect($module->items ?? []);
            
            foreach ($items as $item) {
                if ($item instanceof \App\Models\ModuleItem) {
                    // New structure
                    $type = $item->type;
                    $data = $item->data ?? [];
                    $title = $item->title;
                } else {
                    // Legacy structure
                    $type = $item['type'] ?? 'unknown';
                    $data = $item['data'] ?? [];
                    $title = $data['title'] ?? ucfirst($type);
                }
                $itemTitle = $title . "\n";
                $startIndex = $currentIndex;
                $requests[] = new Docs\Request([
                    'insertText' => [
                        'location' => ['index' => $currentIndex],
                        'text' => $itemTitle,
                    ]
                ]);

                $border = [
                    'color' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 0,
                                'green' => 0,
                                'blue' => 0
                            ]
                        ]
                    ],
                    'width' => ['magnitude' => 2, 'unit' => 'PT'],
                    'dashStyle' => 'SOLID',
                    'padding' => ['magnitude' => 4, 'unit' => 'PT']
                ];

                $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $startIndex,
                            'endIndex' => $currentIndex + strlen($itemTitle) - 1,
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => 'HEADING_2',
                            'shading' => [
                                'backgroundColor' => [
                                    'color' => [
                                        'rgbColor' => [
                                            'red' => 217/255,
                                            'green' => 216/255,
                                            'blue' => 214/255
                                        ]
                                    ]
                                ]
                            ],
                            'borderLeft' => $border,
                            'borderRight' => $border,
                            'borderTop' => $border,
                            'borderBottom' => $border
                        ],
                        'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor,borderLeft,borderRight,borderTop,borderBottom',
                    ]
                ]);
                
                $currentIndex += strlen($itemTitle);

                // 4. Handle each item type with a switch statement
                switch ($type) {
                    case 'overview':
                    case 'page':
                    case 'wrap-up':
                        $content = ($data['content'] ?? 'No content.') . "\n\n";
                         $startIndex = $currentIndex;
                $requests[] = new Docs\Request([
                    'insertText' => [
                        'location' => ['index' => $currentIndex],
                        'text' => $content,
                    ]
                ]);

                $border = [
                    'color' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 0,
                                'green' => 0,
                                'blue' => 0
                            ]
                        ]
                    ],
                    'width' => ['magnitude' => 2, 'unit' => 'PT'],
                    'dashStyle' => 'SOLID',
                    'padding' => ['magnitude' => 4, 'unit' => 'PT']
                ];

                $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $startIndex,
                            'endIndex' => $currentIndex + strlen($content) - 1,
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => 'HEADING_2',
                            'shading' => [
                                'backgroundColor' => [
                                    'color' => [
                                        'rgbColor' => [
                                            'red' => 255/255,
                                            'green' => 255/255,
                                            'blue' => 255/255
                                        ]
                                    ]
                                ]
                            ],
                            'borderLeft' => $border,
                            'borderRight' => $border,
                            'borderTop' => $border,
                            'borderBottom' => $border
                        ],
                        'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor,borderLeft,borderRight,borderTop,borderBottom',
                    ]
                ]);
                
                $currentIndex += strlen($content);
                        break;
                    
                    case 'discussion':
                        $label = $addLabel("Prompt: ");
                        
                          $prompt = ($label . "\n" . $data['prompt'] ?? 'No prompt.') . "\n\n";
                         $startIndex = $currentIndex;
                $requests[] = new Docs\Request([
                    'insertText' => [
                        'location' => ['index' => $currentIndex],
                        'text' => $prompt,
                    ]
                ]);

                $border = [
                    'color' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 0,
                                'green' => 0,
                                'blue' => 0
                            ]
                        ]
                    ],
                    'width' => ['magnitude' => 2, 'unit' => 'PT'],
                    'dashStyle' => 'SOLID',
                    'padding' => ['magnitude' => 4, 'unit' => 'PT']
                ];

                $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $startIndex,
                            'endIndex' => $currentIndex + strlen($prompt) - 1,
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => 'HEADING_2',
                            'shading' => [
                                'backgroundColor' => [
                                    'color' => [
                                        'rgbColor' => [
                                            'red' => 255/255,
                                            'green' => 255/255,
                                            'blue' => 255/255
                                        ]
                                    ]
                                ]
                            ],
                            'borderLeft' => $border,
                            'borderRight' => $border,
                            'borderTop' => $border,
                            'borderBottom' => $border
                        ],
                        'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor,borderLeft,borderRight,borderTop,borderBottom',
                    ]
                ]);
                
                $currentIndex += strlen($prompt);
                if ($data['graded']){
                    $graded = "Graded: " .  "\n" . $data['points']. "points" . "\n\n";
                }else {
                    $graded = "Not Graded" . "\n\n";
                }
                $dates = 'Initial Post Due: ' . ($data['initial_post_due'] ?? 'Not specified.') . "\n" .
                         'Replies Due: ' . ($data['replies_due'] ?? 'Not specified.') . "\n\n";
                           $details = $graded . $dates;
                $startIndex = $currentIndex;
                
                
                $requests[] = new Docs\Request([
                    'insertText' => [
                        'location' => ['index' => $currentIndex],
                        'text' => $details,
                    ]
                ]);

                $border = [
                    'color' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 0,
                                'green' => 0,
                                'blue' => 0
                            ]
                        ]
                    ],
                    'width' => ['magnitude' => 2, 'unit' => 'PT'],
                    'dashStyle' => 'SOLID',
                    'padding' => ['magnitude' => 4, 'unit' => 'PT']
                ];

                $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $startIndex,
                            'endIndex' => $currentIndex + strlen($details) - 1,
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => 'HEADING_2',
                            'shading' => [
                                'backgroundColor' => [
                                    'color' => [
                                        'rgbColor' => [
                                            'red' => 255/255,
                                            'green' => 255/255,
                                            'blue' => 255/255
                                        ]
                                    ]
                                ]
                            ],
                            'borderLeft' => $border,
                            'borderRight' => $border,
                            'borderTop' => $border,
                            'borderBottom' => $border
                        ],
                        'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor,borderLeft,borderRight,borderTop,borderBottom',
                    ]
                ]);
                
                $currentIndex += strlen($details);

                        break;

                    case 'assignment':
                        $purpose = "Purpose: " . "\n" . ($data['purpose'] ?? 'Not specified.') . "\n";
                        $criteria = "Criteria: " . "\n" . ($data['criteria'] ?? 'Not specified.') . "\n\n";
                        $points = "Points: " . ($data['points'] ?? 'Not specified.') . "\n";
                        $dueDate = "Due Date: " . "\n" . ($data['due_date'] ?? 'Not specified.') . "\n\n";
                        $assignment = $purpose . $criteria . $points . $dueDate;
                         $startIndex = $currentIndex;
                $requests[] = new Docs\Request([
                    'insertText' => [
                        'location' => ['index' => $currentIndex],
                        'text' => $assignment,
                    ]
                ]);
                     $border = [
                    'color' => [
                        'color' => [
                            'rgbColor' => [
                                'red' => 0,
                                'green' => 0,
                                'blue' => 0
                            ]
                        ]
                    ],
                    'width' => ['magnitude' => 2, 'unit' => 'PT'],
                    'dashStyle' => 'SOLID',
                    'padding' => ['magnitude' => 4, 'unit' => 'PT']
                ];

                $requests[] = new Docs\Request([
                    'updateParagraphStyle' => [
                        'range' => [
                            'startIndex' => $startIndex,
                            'endIndex' => $currentIndex + strlen($assignment) - 1,
                        ],
                        'paragraphStyle' => [
                            'namedStyleType' => 'HEADING_2',
                            'shading' => [
                                'backgroundColor' => [
                                    'color' => [
                                        'rgbColor' => [
                                            'red' => 255/255,
                                            'green' => 255/255,
                                            'blue' => 255/255
                                        ]
                                    ]
                                ]
                            ],
                            'borderLeft' => $border,
                            'borderRight' => $border,
                            'borderTop' => $border,
                            'borderBottom' => $border
                        ],
                        'fields' => 'namedStyleType,shading.backgroundColor.color.rgbColor,borderLeft,borderRight,borderTop,borderBottom',
                    ]
                ]);
                
                $currentIndex += strlen($assignment);
                        break;
                        
                    case 'quiz':
                        $addLabel('Instructions: ');
                        $addText(($data['instructions'] ?? 'Not specified.') . "\n");
                        
                        // Handle questions from new structure or legacy
                        $questions = [];
                        if ($item instanceof \App\Models\ModuleItem) {
                            $questions = $item->quizQuestions;
                        } elseif (!empty($data['questions'])) {
                            $questions = $data['questions'];
                        }
                        
                        if (!empty($questions)) {
                            foreach ($questions as $index => $question) {
                                if ($question instanceof \App\Models\QuizQuestion) {
                                    $qText = $question->question_text;
                                    $options = $question->options ?? [];
                                } else {
                                    $qText = $question['question'] ?? 'No question text.';
                                    $options = $question['options'] ?? [];
                                }
                                
                                $addText(($index + 1) . ". " . $qText . "\n");
                                if (!empty($options)) {
                                    foreach ($options as $option) {
                                        $addText("   - " . $option . "\n");
                                    }
                                }
                            }
                        }
                        break;
                        
                    default:
                        $addText(json_encode($data, JSON_PRETTY_PRINT) . "\n\n");
                        break;
                }
            }
        }

        return $requests;
    }
}