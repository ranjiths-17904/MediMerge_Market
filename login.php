<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediMerge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.18);
            overflow: hidden;
            width: 100%;
            max-width: 720px;
            display: flex;
            min-height: 520px;
        }

        .auth-image {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .auth-image-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 40px;
        }

        .auth-image h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .auth-image p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .auth-image .logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .auth-forms {
            flex: 1;
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        .form-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group .input-icon {
            position: relative;
        }

        .form-group .input-icon i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
        }

        .form-group .input-icon input {
            padding-left: 50px;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .form-footer {
            text-align: center;
            margin-top: 30px;
        }

        .form-footer p {
            color: #666;
            margin-bottom: 15px;
        }

        .toggle-form {
            background: none;
            border: none;
            color: #667eea;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 1rem;
        }

        .toggle-form:hover {
            color: #5a6fd8;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
            display: none;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #cfc;
            display: none;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                max-width: 420px;
            }

            .auth-image {
                padding: 30px 20px;
            }

            .auth-image h2 {
                font-size: 2rem;
            }

            .auth-forms {
                padding: 24px 18px;
            }

            .form-header h2 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                border-radius: 20px;
                margin: 10px;
            }

            .auth-forms {
                padding: 20px;
            }

            .form-group input {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <a href="medico.html" class="back-home">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="auth-container">
        <div class="auth-image">
            <div class="auth-image-content">
                <div class="logo">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h2>MediMerge</h2>
                <p>Your trusted healthcare partner. Access quality medicines and healthcare products with ease and convenience.</p>
            </div>
        </div>

        <div class="auth-forms">
            <!-- Login Form -->
            <div class="form-container active" id="loginForm">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account to continue</p>
                </div>

                <div id="loginError" class="error-message"></div>
                <div id="loginSuccess" class="success-message"></div>

                <form id="loginFormElement">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="loginUsername" name="username" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" name="password" required>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <span class="loading" style="display: none;"></span>
                    </button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account?</p>
                    <button class="toggle-form" onclick="toggleForm('signup')">Create Account</button>
                    <p style="margin-top: 10px;"><a href="#" onclick="showForgotPassword()" style="color: #667eea; text-decoration: none;">Forgot Password?</a></p>
                </div>
            </div>

            <!-- Signup Form -->
            <div class="form-container" id="signupForm">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Join MediMerge for the best healthcare experience</p>
                </div>

                <div id="signupError" class="error-message"></div>
                <div id="signupSuccess" class="success-message"></div>

                <form id="signupFormElement">
                    <div class="form-group">
                        <label for="signupUsername">Username</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="signupUsername" name="username" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupEmail">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="signupEmail" name="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupPassword">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="signupPassword" name="password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupConfirmPassword">Confirm Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="signupConfirmPassword" name="confirm_password" required>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="signupBtn">
                        <span class="btn-text">Create Account</span>
                        <span class="loading" style="display: none;"></span>
                    </button>
                </form>

                <div class="form-footer">
                    <p>Already have an account?</p>
                    <button class="toggle-form" onclick="toggleForm('login')">Sign In</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm(formType) {
            const loginForm = document.getElementById('loginForm');
            const signupForm = document.getElementById('signupForm');
            
            if (formType === 'signup') {
                loginForm.classList.remove('active');
                signupForm.classList.add('active');
            } else {
                signupForm.classList.remove('active');
                loginForm.classList.add('active');
            }
            
            // Clear messages
            clearMessages();
        }

        function clearMessages() {
            document.querySelectorAll('.error-message, .success-message').forEach(msg => {
                msg.style.display = 'none';
                msg.textContent = '';
            });
        }

        function showMessage(elementId, message, type) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.style.display = 'block';
            element.className = type === 'error' ? 'error-message' : 'success-message';
        }

        function setLoading(buttonId, isLoading) {
            const button = document.getElementById(buttonId);
            const btnText = button.querySelector('.btn-text');
            const loading = button.querySelector('.loading');
            
            if (isLoading) {
                btnText.style.display = 'none';
                loading.style.display = 'inline-block';
                button.disabled = true;
            } else {
                btnText.style.display = 'inline';
                loading.style.display = 'none';
                button.disabled = false;
            }
        }

        // Login Form Handler
        document.getElementById('loginFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('loginUsername').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!username || !password) {
                showMessage('loginError', 'Please fill in all fields', 'error');
                return;
            }
            
            setLoading('loginBtn', true);
            clearMessages();
            
            try {
                const response = await fetch('./auth/login_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('loginSuccess', 'Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        if (data.isAdmin) {
                            window.location.href = './dashboard.php';
                        } else {
                            window.location.href = './medico.html';
                        }
                    }, 1500);
                } else {
                    showMessage('loginError', data.message || 'Login failed', 'error');
                }
            } catch (error) {
                showMessage('loginError', 'Network error. Please try again.', 'error');
            } finally {
                setLoading('loginBtn', false);
            }
        });

        // Signup Form Handler
        document.getElementById('signupFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('signupUsername').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirmPassword').value;
            
            if (!username || !email || !password || !confirmPassword) {
                showMessage('signupError', 'Please fill in all fields', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('signupError', 'Passwords do not match', 'error');
                return;
            }
            
            if (password.length < 6) {
                showMessage('signupError', 'Password must be at least 6 characters long', 'error');
                return;
            }
            
            setLoading('signupBtn', true);
            clearMessages();
            
            try {
                const response = await fetch('./auth/signup_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ username, email, password, confirm_password: confirmPassword })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('signupSuccess', 'Account created successfully! Please sign in.', 'success');
                    setTimeout(() => {
                        toggleForm('login');
                    }, 2000);
                } else {
                    showMessage('signupError', data.message || 'Registration failed', 'error');
                }
            } catch (error) {
                showMessage('signupError', 'Network error. Please try again.', 'error');
            } finally {
                setLoading('signupBtn', false);
            }
        });

        function showForgotPassword() {
            const email = prompt('Enter your email address to reset password:');
            if (email) {
                alert('Password reset link has been sent to ' + email + '\n\nNote: This is a demo. In production, implement actual password reset functionality.');
            }
        }

        // Check if user is already logged in
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch('./session.php');
                const data = await response.json();
                
                if (data.username) {
                    // User is already logged in, redirect
                    if (data.isAdmin) {
                        window.location.href = './dashboard.php';
                    } else {
                        window.location.href = './medico.html';
                    }
                }
            } catch (error) {
                console.log('Session check failed, continuing with login form');
            }
        });
    </script>
</body>
</html>
