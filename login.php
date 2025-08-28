<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>MediMerge - Login & Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body { height: 100%; }
        body { overflow: auto; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 980px;
            min-height: 620px;
            display: flex;
        }

        .form-container {
            flex: 1;
            padding: 40px;
            position: relative;
            overflow-y: auto;
            max-height: calc(100vh - 120px);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .logo-section h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .logo-section h1 span {
            color: #11b671;
        }

        .logo-section p {
            color: #666;
            font-size: 14px;
        }

        .toggle-container {
            display: flex;
            background: #f0f0f0;
            border-radius: 50px;
            padding: 5px;
            margin-bottom: 30px;
            position: relative;
        }

        .toggle-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .toggle-btn.active {
            color: white;
        }

        .toggle-slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 5px);
            height: calc(100% - 10px);
            background: linear-gradient(135deg, #11b671, #0ea55d);
            border-radius: 25px;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .form {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .input-group input:focus {
            outline: none;
            border-color: #11b671;
            box-shadow: 0 0 0 3px rgba(17, 182, 113, 0.1);
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 45px;
            color: #999;
            transition: all 0.3s ease;
        }

        .input-group input:focus + i {
            color: #11b671;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #11b671;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(17, 182, 113, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e1e1;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #666;
            font-size: 14px;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            background: white;
            color: #333;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .social-btn:hover {
            border-color: #11b671;
            color: #11b671;
            transform: translateY(-2px);
        }

        .switch-form {
            text-align: center;
            margin-top: 20px;
        }

        .switch-form a {
            color: #11b671;
            text-decoration: none;
            font-weight: 600;
        }

        .switch-form a:hover {
            text-decoration: underline;
        }

        .home-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .home-link:hover {
            transform: translateX(-5px);
        }

        .illustration {
            flex: 1;
            background: linear-gradient(135deg, #11b671, #0ea55d);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .illustration::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: float 15s ease-in-out infinite reverse;
        }

        .illustration-content {
            text-align: center;
            color: white;
            z-index: 1;
            position: relative;
        }

        .illustration-content i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .illustration-content h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .illustration-content p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 520px;
            }

            .illustration {
                display: none;
            }

            .form-container { padding: 30px 20px; max-height: none; }

            .social-login {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .form-container {
                padding: 20px 15px;
            }

            .logo-section h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <a href="medico.html" class="home-link">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <div class="container">
        <div class="form-container">
            <div class="logo-section">
                <img src="./Images/MEDI_MERGE_LOGO.png" alt="MediMerge">
                <h1>Medi<span>Merge</span></h1>
                <p>Your Health, Our Priority</p>
            </div>

            <div class="toggle-container">
                <button class="toggle-btn active" data-form="login">Login</button>
                <button class="toggle-btn" data-form="signup">Sign Up</button>
                <div class="toggle-slider"></div>
            </div>

            <!-- Login Form -->
            <form action="login1.php" method="POST" class="form active" id="login-form">
                <div class="input-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="submit-btn">Login</button>
                
                <div class="divider">
                    <span>or continue with</span>
                </div>
                
                <div class="social-login">
                    <button type="button" class="social-btn">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button type="button" class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                </div>
            </form>

            <!-- Signup Form -->
            <form action="signup.php" method="POST" class="form" id="signup-form">
                <div class="input-group">
                    <label for="signup-email">Email Address</label>
                    <input type="email" id="signup-email" name="email" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group">
                    <label for="signup-username">Username</label>
                    <input type="text" id="signup-username" name="username" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-group">
                    <label for="signup-password">Password</label>
                    <input type="password" id="signup-password" name="password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div class="input-group">
                    <label for="signup-confirm">Confirm Password</label>
                    <input type="password" id="signup-confirm" name="confirm_password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" class="submit-btn">Create Account</button>
                
                <div class="divider">
                    <span>or continue with</span>
                </div>
                
                <div class="social-login">
                    <button type="button" class="social-btn">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button type="button" class="social-btn">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                </div>
            </form>
        </div>

        <div class="illustration">
            <div class="illustration-content">
                <i class="fas fa-heartbeat"></i>
                <h2>Welcome to MediMerge</h2>
                <p>Your trusted partner for all your healthcare needs. Get the best medicines delivered to your doorstep.</p>
            </div>
        </div>
    </div>

    <script>
        // Show success message if redirected from signup
        (function(){
            const params=new URLSearchParams(window.location.search);
            if(params.get('signup')==='success' && window.navbar && window.navbar.showToast){
                window.navbar.showToast('Signup successful. Please login');
            }
        })();
        // Form Toggle Functionality
        const toggleBtns = document.querySelectorAll('.toggle-btn');
        const toggleSlider = document.querySelector('.toggle-slider');
        const forms = document.querySelectorAll('.form');

        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const formType = btn.dataset.form;
                
                // Update toggle buttons
                toggleBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Move slider
                if (formType === 'signup') {
                    toggleSlider.style.left = 'calc(50% + 2.5px)';
                } else {
                    toggleSlider.style.left = '5px';
                }
                
                // Show/hide forms
                forms.forEach(form => {
                    form.classList.remove('active');
                    if (form.id === `${formType}-form`) {
                        form.classList.add('active');
                    }
                });
            });
        });

        // Form Validation
        const signupForm = document.getElementById('signup-form');
        const signupPassword = document.getElementById('signup-password');
        const signupConfirm = document.getElementById('signup-confirm');
        const loginForm = document.getElementById('login-form');

        signupForm.addEventListener('submit', (e) => {
            if (signupPassword.value !== signupConfirm.value) {
                e.preventDefault();
                if(window.navbar && window.navbar.showToast){ window.navbar.showToast('Passwords do not match'); }
                return;
            }
            
            if (signupPassword.value.length < 6) {
                e.preventDefault();
                if(window.navbar && window.navbar.showToast){ window.navbar.showToast('Password must be at least 6 characters'); }
                return;
            }
        });

        loginForm.addEventListener('submit',(e)=>{
            const email=document.getElementById('login-email').value.trim();
            const pwd=document.getElementById('login-password').value;
            const valid=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            if(!valid){ e.preventDefault(); if(window.navbar && window.navbar.showToast){ window.navbar.showToast('Enter a valid email'); } return; }
            if(pwd.length<6){ e.preventDefault(); if(window.navbar && window.navbar.showToast){ window.navbar.showToast('Password must be at least 6 characters'); } return; }
        });

        // Input Focus Effects
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'scale(1)';
            });
        });

        // Social Login Buttons (Placeholder)
        const socialBtns = document.querySelectorAll('.social-btn');
        socialBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                if(window.navbar && window.navbar.showToast){
                    window.navbar.showToast('Social login coming soon');
                }
            });
        });
    </script>
</body>
</html>
