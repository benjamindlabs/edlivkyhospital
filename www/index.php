<?php
// EdlivkyHospital - Modern Landing Page
$pageTitle = "EdlivkyHospital - Excellence in Healthcare Since 2000";
$pageDescription = "Where sophisticated medical expertise meets personalized patient care. Our commitment to excellence has made us a trusted partner in your health journey.";
$pageKeywords = "EdlivkyHospital, healthcare excellence, medical expertise, patient care, hospital services, emergency care, medical professionals";

// Include public header
include 'includes/public_header.php';
?>

<!-- Custom Styles for Modern Design -->
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f3e8ff 100%);
    }
    .gradient-text {
        background: linear-gradient(135deg, #06b6d4 0%, #ec4899 50%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .card-gradient-1 {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    .card-gradient-2 {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    }
    .card-gradient-3 {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .stat-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .service-icon {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }
</style>

<!-- Hero Section -->
<section id="home" class="gradient-bg min-h-screen flex items-center relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-10 w-32 h-32 bg-cyan-400 rounded-full"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-pink-400 rounded-full"></div>
        <div class="absolute bottom-32 left-1/4 w-20 h-20 bg-purple-400 rounded-full"></div>
        <div class="absolute bottom-20 right-1/3 w-28 h-28 bg-cyan-300 rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div>
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-cyan-100 text-cyan-800 rounded-full text-sm font-medium mb-6">
                    <i class="fas fa-award mr-2"></i>
                    Excellence in Healthcare Since 2000
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Elegant
                    <span class="gradient-text block">Healthcare</span>
                    Solutions
                </h1>

                <!-- Description -->
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Where sophisticated medical expertise meets personalized patient care. Our commitment to
                    excellence has made us a trusted partner in your health journey.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <button class="card-gradient-1 text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition duration-300 flex items-center justify-center">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Consultation
                    </button>
                    <button class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-50 transition duration-300 flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>
                        Virtual Tour
                    </button>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">98%</div>
                        <div class="text-sm text-gray-600">Patient Satisfaction</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">200+</div>
                        <div class="text-sm text-gray-600">Medical Professionals</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">20+</div>
                        <div class="text-sm text-gray-600">Years Experience</div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Image/Visual -->
            <div class="relative">
                <div class="stat-card rounded-3xl p-8 shadow-xl">
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="bg-cyan-50 rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 bg-cyan-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="font-semibold text-gray-900">Certified Excellence</div>
                            <div class="text-sm text-gray-600">JCI Accredited</div>
                        </div>
                        <div class="bg-pink-50 rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div class="font-semibold text-gray-900">24/7 Support</div>
                            <div class="text-sm text-gray-600">Always Here</div>
                        </div>
                    </div>

                    <!-- Hospital Image -->
                    <div class="relative rounded-2xl h-64 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                             alt="Modern hospital facility with caring medical staff"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="font-medium">State-of-the-Art Medical Facility</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Redefining Healthcare Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                Redefining Healthcare
            </h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                At EdlivkyHospital, we believe healthcare should be accessible, compassionate, and innovative.
                Our integrated approach combines cutting-edge medical technology with personalized patient care.
            </p>
        </div>
    </div>
</section>

<!-- Medical Expertise Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                Our Medical Expertise
            </h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                Comprehensive healthcare services delivered by our team of board-certified
                specialists using the latest medical advances.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Cardiovascular Excellence -->
            <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition duration-300 group">
                <div class="relative mb-8">
                    <div class="card-gradient-1 w-full h-4 rounded-full mb-6"></div>
                    <!-- Doctor Image -->
                    <div class="w-20 h-20 rounded-2xl overflow-hidden mx-auto mb-4">
                        <img src="https://i.ibb.co/nsWvFPM3/7baed3ee85756fa875a97db1b1c38f46.jpg"
                             alt="Cardiologist specialist"
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Cardiovascular Excellence</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    Advanced cardiac care including interventional procedures, heart surgery,
                    and comprehensive cardiac rehabilitation programs.
                </p>
            </div>

            <!-- Neurological Care -->
            <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition duration-300 group">
                <div class="relative mb-8">
                    <div class="card-gradient-2 w-full h-4 rounded-full mb-6"></div>
                    <!-- Doctor Image -->
                    <div class="w-20 h-20 rounded-2xl overflow-hidden mx-auto mb-4">
                        <img src="https://i.ibb.co/xSszzSJf/ce1b10bb8d8419b3194efac0a95a738f.jpg"
                             alt="Neurologist specialist"
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Neurological Care</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    Comprehensive neurological services from stroke care to complex brain
                    surgery with cutting-edge imaging technology.
                </p>
            </div>

            <!-- Emergency Medicine -->
            <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition duration-300 group">
                <div class="relative mb-8">
                    <div class="card-gradient-3 w-full h-4 rounded-full mb-6"></div>
                    <!-- Emergency Team Image -->
                    <div class="w-20 h-20 rounded-2xl overflow-hidden mx-auto mb-4">
                        <img src="https://i.ibb.co/mZbmpKy/ebe0e329a1ab88d8cbbd96798f731d10.jpg"
                             alt="Emergency medical team"
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Emergency Medicine</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    24/7 emergency services with rapid response times and state-of-the-art
                    trauma care facilities.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose EdlivkyHospital Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left Content -->
            <div>
                <div class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium mb-6">
                    <i class="fas fa-star mr-2"></i>
                    Trusted Healthcare Partner
                </div>

                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Excellence in Every
                    <span class="gradient-text">Patient Interaction</span>
                </h2>

                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    For over two decades, EdlivkyHospital has been at the forefront of healthcare excellence,
                    providing compassionate care and innovative medical solutions to our community.
                </p>

                <!-- Feature List -->
                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-cyan-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Board-certified specialists across all major disciplines</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-pink-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span class="text-gray-700 font-medium">State-of-the-art medical technology and equipment</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Personalized treatment plans for every patient</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-cyan-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span class="text-gray-700 font-medium">24/7 emergency care with rapid response</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="about.php" class="card-gradient-1 text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition duration-300 text-center">
                        Learn More About Us
                    </a>
                    <a href="contact.php" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:bg-gray-50 transition duration-300 text-center">
                        Contact Us
                    </a>
                </div>
            </div>

            <!-- Right Content - Medical Team Image & Stats -->
            <div class="relative">
                <!-- Medical Team Image -->
                <div class="stat-card rounded-3xl p-6 mb-6">
                    <div class="relative rounded-2xl h-48 overflow-hidden mb-4">
                        <img src="https://ibb.co/Dfbb17T0"><img src="https://i.ibb.co/whLLdKj5/SQUAD.jpg"
                             alt="EdlivkyHospital medical team - doctors and nurses working together"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <h4 class="font-bold text-lg">Our Dedicated Team</h4>
                            <p class="text-sm opacity-90">Committed to your health and wellbeing</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Mission Card -->
                    <div class="stat-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 card-gradient-1 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-heart text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">Our Mission</h4>
                        <p class="text-gray-600 text-sm">Exceptional healthcare with compassion and excellence</p>
                    </div>

                    <!-- Vision Card -->
                    <div class="stat-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 card-gradient-2 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-eye text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">Our Vision</h4>
                        <p class="text-gray-600 text-sm">Leading healthcare provider setting quality standards</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Patient Testimonials Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 bg-pink-100 text-pink-800 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-heart mr-2"></i>
                Patient Stories
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                Trusted by Thousands of
                <span class="gradient-text">Happy Patients</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                Don't just take our word for it. Here's what our patients have to say about their experience with EdlivkyHospital.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="stat-card rounded-3xl p-8 text-center">
                <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-6">
                    <img src="../public/assets/images/testimonials/patient1.jpg"
                         alt="Sarah Johnson - Happy Patient"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex justify-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic leading-relaxed">
                    "The cardiac team at EdlivkyHospital saved my life. Their expertise, compassion, and state-of-the-art facilities made all the difference in my recovery."
                </p>
                <h4 class="font-bold text-gray-900">Sarah Johnson</h4>
                <p class="text-gray-500 text-sm">Cardiac Patient</p>
            </div>

            <!-- Testimonial 2 -->
            <div class="stat-card rounded-3xl p-8 text-center">
                <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-6">
                    <img src="../public/assets/images/testimonials/patient2.jpg"
                         alt="Michael Chen - Happy Patient"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex justify-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic leading-relaxed">
                    "From emergency care to follow-up treatment, every staff member showed incredible professionalism and genuine care for my wellbeing."
                </p>
                <h4 class="font-bold text-gray-900">Michael Chen</h4>
                <p class="text-gray-500 text-sm">Emergency Patient</p>
            </div>

            <!-- Testimonial 3 -->
            <div class="stat-card rounded-3xl p-8 text-center">
                <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-6">
                    <img src="../public/assets/images/testimonials/patient3.jpg"
                         alt="Emily Rodriguez - Happy Patient"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex justify-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic leading-relaxed">
                    "The neurological team provided exceptional care during my treatment. I felt supported and confident throughout my entire journey."
                </p>
                <h4 class="font-bold text-gray-900">Emily Rodriguez</h4>
                <p class="text-gray-500 text-sm">Neurology Patient</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left Content -->
            <div>
                <div class="inline-flex items-center px-4 py-2 bg-cyan-100 text-cyan-800 rounded-full text-sm font-medium mb-6">
                    <i class="fas fa-envelope mr-2"></i>
                    Get in Touch
                </div>

                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Ready to Start Your
                    <span class="gradient-text">Health Journey?</span>
                </h2>

                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Contact our team today to schedule a consultation or learn more about our comprehensive healthcare services.
                    We're here to support you every step of the way.
                </p>

                <!-- Contact Info Cards -->
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm">
                        <div class="w-12 h-12 card-gradient-1 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Call Us</h4>
                            <p class="text-gray-600">+1 (234) 567-890</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm">
                        <div class="w-12 h-12 card-gradient-2 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Email Us</h4>
                            <p class="text-gray-600">info@edlivkyhospital.com</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-2xl shadow-sm">
                        <div class="w-12 h-12 card-gradient-3 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Visit Us</h4>
                            <p class="text-gray-600">123 Healthcare Avenue, Medical District</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Contact Form -->
            <div class="stat-card rounded-3xl p-8 shadow-xl">
                <form id="contactForm" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" id="firstName" name="firstName" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                                   placeholder="Enter your first name">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                                   placeholder="Enter your last name">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                               placeholder="Enter your email address">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                               placeholder="Enter your phone number">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select id="subject" name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300">
                            <option value="">Select a subject</option>
                            <option value="appointment">Schedule Appointment</option>
                            <option value="emergency">Emergency Services</option>
                            <option value="general">General Inquiry</option>
                            <option value="insurance">Insurance Questions</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                                  placeholder="Tell us how we can help you..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full card-gradient-1 text-white px-8 py-4 rounded-2xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition duration-300">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Contact Banner -->
<section class="py-8 bg-gradient-to-r from-red-500 to-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between text-center md:text-left">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-ambulance text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">24/7 Emergency Services</h3>
                    <p class="text-red-100">Always here when you need us most</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+1234567890" class="bg-white text-red-600 px-6 py-3 rounded-xl font-semibold hover:bg-red-50 transition duration-300">
                    <i class="fas fa-phone mr-2"></i>
                    Call Emergency: (234) 567-890
                </a>
                <a href="../public/auth/login.php" class="border-2 border-white text-white px-6 py-3 rounded-xl font-semibold hover:bg-white hover:text-red-600 transition duration-300">
                    <i class="fas fa-user-md mr-2"></i>
                    Staff Portal
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include public footer
include 'includes/public_footer.php';
?>
