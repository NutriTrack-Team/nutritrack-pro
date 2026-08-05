<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriTrack Pro - Your Personal Health & Diet Management System</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <span>🍏</span> NutriTrack Pro
            </div>
            <ul class="navbar-menu">
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><button class="btn btn-primary" onclick="openLoginModal()">Sign In</button></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Transform Your Health Journey</h1>
                <p>Track your nutrition, connect with expert dietitians, and achieve your health goals with NutriTrack Pro</p>
                <div class="hero-buttons">
                    <button class="btn btn-primary btn-lg" onclick="openSignupModal()">Get Started Free</button>
                    <button class="btn btn-outline btn-lg" onclick="openLoginModal()">Sign In</button>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-icon">🥗</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="text-center">Why Choose NutriTrack Pro?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Smart Tracking</h3>
                    <p>Track calories, macros, water intake, and meals effortlessly</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👨‍⚕️</div>
                    <h3>Expert Dietitians</h3>
                    <p>Connect with certified dietitians for personalized guidance</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🍳</div>
                    <h3>Smart Pantry</h3>
                    <p>Get recipe suggestions based on your available ingredients</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3>Progress Analytics</h3>
                    <p>Visualize your journey with detailed charts and reports</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Personalized Plans</h3>
                    <p>Get custom diet plans tailored to your goals</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Direct Messaging</h3>
                    <p>Chat with your dietitian anytime, anywhere</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Sign In to NutriTrack Pro</h2>
                <button class="modal-close" onclick="closeLoginModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label class="form-label">Select Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user">User</option>
                            <option value="dietitian">Dietitian</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div id="loginError" class="alert alert-danger hidden"></div>
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
                <p class="text-center mt-2">Don't have an account? <a href="#" onclick="openSignupModal(); closeLoginModal();">Sign Up</a></p>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Join NutriTrack Pro</h2>
                <button class="modal-close" onclick="closeSignupModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="role-selection">
                    <h3>Select Your Role</h3>
                    <div class="role-buttons">
                        <button class="role-btn" onclick="selectRole('user')">
                            <div class="role-icon">👤</div>
                            <div>User</div>
                        </button>
                        <button class="role-btn" onclick="selectRole('dietitian')">
                            <div class="role-icon">👨‍⚕️</div>
                            <div>Dietitian</div>
                        </button>
                    </div>
                </div>
                
                <!-- User Signup Form -->
                <form id="userSignupForm" class="signup-form hidden" onsubmit="handleUserSignup(event)">
                    <h3>User Registration</h3>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control" min="1" max="120" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" name="height" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" name="weight" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Target Weight (kg)</label>
                                <input type="number" name="target_weight" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Activity Level</label>
                        <select name="activity_level" class="form-control" required>
                            <option value="">Select Activity Level</option>
                            <option value="Sedentary">Sedentary (little or no exercise)</option>
                            <option value="Lightly Active">Lightly Active (1-3 days/week)</option>
                            <option value="Moderately Active">Moderately Active (3-5 days/week)</option>
                            <option value="Very Active">Very Active (6-7 days/week)</option>
                            <option value="Extra Active">Extra Active (twice per day)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Medical Conditions (if any)</label>
                        <input type="text" name="medical_conditions" class="form-control" placeholder="e.g., Diabetes, Hypertension">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dietary Preference</label>
                        <select name="dietary_preference" class="form-control">
                            <option value="">Select Preference</option>
                            <option value="Vegetarian">Vegetarian</option>
                            <option value="Vegan">Vegan</option>
                            <option value="Pescatarian">Pescatarian</option>
                            <option value="Non-Vegetarian">Non-Vegetarian</option>
                            <option value="Keto">Keto</option>
                            <option value="Paleo">Paleo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Allergies (if any)</label>
                        <input type="text" name="allergies" class="form-control" placeholder="e.g., Peanuts, Dairy">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="auto_assign_dietitian" value="1">
                            Auto assign me a dietitian
                        </label>
                    </div>
                    <div id="userSignupError" class="alert alert-danger hidden"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeSignupModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>

                <!-- Dietitian Signup Form -->
                <form id="dietitianSignupForm" class="signup-form hidden" onsubmit="handleDietitianSignup(event)">
                    <h3>Dietitian Registration</h3>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Passing Institute</label>
                        <input type="text" name="passing_institute" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Degrees / Qualifications</label>
                        <input type="text" name="degrees" class="form-control" placeholder="e.g., MBBS, MSc in Nutrition" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Experience (years)</label>
                        <input type="number" name="experience" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" class="form-control" required>
                    </div>
                    <div id="dietitianSignupError" class="alert alert-danger hidden"></div>
                    <p class="alert alert-info">Note: Your registration will be submitted for admin approval.</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeSignupModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit for Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>NutriTrack Pro</h3>
                <p>Your trusted partner in health and nutrition management.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Profile Editor Login</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Give Feedback</a></li>
                    <li><a href="#">Accessibility Statement</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <ul>
                    <li>Email: support@nutritrackpro.com</li>
                    <li>Phone: +880 1XXX-XXXXXX</li>
                    <li>Address: Dhaka, Bangladesh</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 NutriTrack Pro. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
