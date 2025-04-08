<?php

$allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?? 'https://aiproject-o0ky.onrender.com';
header("Access-Control-Allow-Origin: $allowedOrigin");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

header("Access-Control-Allow-Headers: Content-Type");

$apiKey = $_ENV['GEMINI_API_KEY'];

if (empty($apiKey)) {
    echo json_encode(['response' => 'Error: Gemini API key not found.']);
    exit;
}

$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($message)) {
    echo json_encode(['response' => 'Please enter a message.']);
    exit;
}

// Check if the message is related to motivational quotes
$isMotivational = isMotivationalPrompt($message);
$isagreeting= isagreetingprompt($message);
if ($isMotivational || $isagreeting) {
        if($isMotivational){

            $prompt = "Generate a, unformatted motivational quote related to: " . $message . 
                  ". Respond with just the quote, without any markdown, stars, or special formatting.";
        }
        else{
            $prompt = "Respond with a friendly greeting message based on: " . $message . 
                  ". Keep it concise and positive, without any special formatting.";
        }
    

    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $jsonData = json_encode($data);

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Keep SSL verification on in production

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['response' => 'cURL error: ' . curl_error($ch)]);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode >= 400) {
            echo json_encode(['response' => 'HTTP error: ' . $httpCode . "\nResponse: " . $response]);
        } else {
            $responseData = json_decode($response, true);

            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedQuote = $responseData['candidates'][0]['content']['parts'][0]['text'];
                // Strip markdown formatting
                $generatedQuote = preg_replace('/[\*\_\#\~\`]/', '', $generatedQuote); // Remove markdown symbols
                $generatedQuote = preg_replace('/\n+/', ' ', $generatedQuote); // Replace multiple newlines with space
                $generatedQuote = trim($generatedQuote); // Remove extra spaces
                echo json_encode(['response' => $generatedQuote]);
            } else {
                echo json_encode(['response' => 'Could not extract quote from response.']);
            }
        }
    }

    curl_close($ch);
} else {
    echo json_encode(['response' => 'I specialize in generating motivational quotes. Please provide a topic or ask for inspiration!']);
}

// Function to determine if a prompt is motivational (basic example)
function isMotivationalPrompt($message) {
    $keywords = [
        'motivate', 'inspire', 'quote', 'encourage', 'motivation', 'inspiration', 'uplift', 
        'empower', 'positivity', 'success', 'determination', 'perseverance', 'hope', 'dream', 
        'goal', 'confidence', 'energy', 'focus', 'achievement', 'ambition', 'aspiration', 
        'believe', 'bravery', 'courage', 'drive', 'enthusiasm', 'faith', 'growth', 'happiness', 
        'hard work', 'optimism', 'passion', 'patience', 'persistence', 'potential', 'progress', 
        'resilience', 'self-belief', 'self-confidence', 'self-discipline', 'self-esteem', 
        'self-improvement', 'strength', 'success mindset', 'tenacity', 'vision', 'willpower', 
        'zeal', 'accomplishment', 'attitude', 'breakthrough', 'clarity', 'commitment', 
        'dedication', 'devotion', 'discipline', 'empowerment', 'endurance', 'fortitude', 
        'grit', 'hopefulness', 'initiative', 'inner strength', 'inspirational', 'leadership', 
        'mindset', 'motivation energy', 'overcome', 'persevere', 'positivity boost', 
        'power', 'purpose', 'self-motivation', 'spirit', 'strength of character', 
        'success-driven', 'triumph', 'unstoppable', 'victory','boost', 'will to succeed,'
    ];
    $message = strtolower($message);
    foreach ($keywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return true;
        }
    }
    return false;
}
function isagreetingprompt($message){
    $keywords=[
        'hi', 'hello', 'hey', 'greetings', 'good morning', 'good afternoon', 'good evening', 'howdy', 'what\'s up', 'yo', 'hi there', 'hello there', 'hey there', 'hiya', 'sup', 'morning','name', 'afternoon', 'evening'
    ];
    $message = strtolower($message);
    foreach ($keywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return true;
        }
    }
    return false;
}
?>
