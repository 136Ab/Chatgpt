```
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gemini API Key
define('GEMINI_API_KEY', 'AIzaSyCS1xSEgDXOrtJuB4F1InEQOlP0nywNB3o');

// Determine page
$page = isset($_GET['page']) ? $_GET['page'] : 'chat';

// Initialize variables
$response = '';
$error = '';
$signupError = '';
$signupSuccess = '';
$loginError = '';
$loginSuccess = '';

// Handle chat request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'chat' && isset($_POST['prompt'])) {
    $prompt = trim($_POST['prompt']);
    if (!empty($prompt)) {
        try {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY;
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($url);
            if ($ch === false) {
                throw new Exception('Failed to initialize cURL.');
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $apiResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($apiResponse === false) {
                throw new Exception('cURL error: ' . curl_error($ch));
            }
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception('API request failed with status code: ' . $httpCode);
            }

            $decodedResponse = json_decode($apiResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            if (isset($decodedResponse['candidates'][0]['content']['parts'][0]['text'])) {
                $response = $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
            } else {
                throw new Exception('Invalid API response format.');
            }
        } catch (Exception $e) {
            $error = 'Error: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = 'Please enter a prompt.';
    }
}

// Handle signup request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'signup') {
    try {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username)) {
            throw new Exception('Username is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }

        // Simulate signup processing (replace with database logic if needed)
        $signupSuccess = 'Signup successful! Welcome, ' . htmlspecialchars($username) . '.';
    } catch (Exception $e) {
        $signupError = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
    try {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email)) {
            throw new Exception('Email is required.');
        }
        if (empty($password)) {
            throw new Exception('Password is required.');
        }

        // Simulate login processing (replace with database logic if needed)
        $loginSuccess = 'Login successful!';
    } catch (Exception $e) {
        $loginError = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Clone - <?php echo ucfirst($page); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .container {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        .header {
            padding: 15px;
            background: #007bff;
            color: white;
            text-align: center;
            font-size: 1.2em;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .body {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
        }
        .user-message {
            background: #007bff;
            color: white;
            margin-left: auto;
        }
        .bot-message {
            background: #e9ecef;
            color: #333;
        }
        .error-message, .success-message {
            text-align: center;
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        .form-container {
            padding: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        .footer {
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        #prompt {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .nav-link {
            text-align: center;
            margin: 10px 0;
        }
        .nav-link a {
            color: #007bff;
            text-decoration: none;
        }
        .nav-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container {
                height: 100vh;
                border-radius: 0;
            }
            .footer {
                flex-direction: column;
            }
            #prompt {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">ChatGPT Clone - <?php echo ucfirst($page); ?></div>
        <div class="body" id="body">
            <?php if ($page === 'chat'): ?>
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (!empty($response)): ?>
                    <div class="message user-message"><?php echo htmlspecialchars($_POST['prompt']); ?></div>
                    <div class="message bot-message"><?php echo nl2br(htmlspecialchars($response)); ?></div>
                <?php endif; ?>
                <div class="nav-link"><a href="?page=signup">Don't have an account? Sign up</a></div>
                <div class="nav-link"><a href="?page=login">Already have an account? Log in</a></div>
            <?php elseif ($page === 'signup'): ?>
                <?php if (!empty($signupError)): ?>
                    <div class="error-message"><?php echo $signupError; ?></div>
                <?php endif; ?>
                <?php if (!empty($signupSuccess)): ?>
                    <div class="success-message"><?php echo $signupSuccess; ?></div>
                <?php endif; ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="page" value="signup">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit">Sign Up</button>
                    </form>
                    <div class="nav-link"><a href="?page=chat">Back to Chat</a></div>
                    <div class="nav-link"><a href="?page=login">Already have an account? Log in</a></div>
                </div>
            <?php elseif ($page === 'login'): ?>
                <?php if (!empty($loginError)): ?>
                    <div class="error-message"><?php echo $loginError; ?></div>
                <?php endif; ?>
                <?php if (!empty($loginSuccess)): ?>
                    <div class="success-message"><?php echo $loginSuccess; ?></div>
                <?php endif; ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="page" value="login">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit">Log In</button>
                    </form>
                    <div class="nav-link"><a href="?page=chat">Back to Chat</a></div>
                    <div class="nav-link"><a href="?page=signup">Don't have an account? Sign up</a></div>
                </div>
            <?php else: ?>
                <div class="error-message">Page not found.</div>
                <div class="nav-link"><a href="?page=chat">Back to Chat</a></div>
            <?php endif; ?>
        </div>
        <?php if ($page === 'chat'): ?>
            <div class="footer">
                <form method="POST">
                    <input type="hidden" name="page" value="chat">
                    <input type="text" id="prompt" name="prompt" placeholder="Type your message..." autocomplete="off">
                    <button type="submit">Send</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.getElementById('body');
            if (body) {
                body.scrollTop = body.scrollHeight;
            }

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const prompt = document.getElementById('prompt');
                    if (prompt && !prompt.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a message.');
                    }
                });
            }
        });
    </script>
</body>
</html>
```
