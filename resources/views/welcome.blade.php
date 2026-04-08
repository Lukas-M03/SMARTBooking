<x-layout>
    <div class="hero">
        <h1 class="text-gray-700">Welcome to SMART Booking</h1>
        <p class="text-gray-700"> 
            Streamlined Meeting Appointments & Resource Tracking - Simplifying student-adviser bookings with automated matching, real-time availability, and intelligent notifications.
        </p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
            <a href="{{ route('login') }}" class="btn-secondary">Sign In</a>
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <h3>🎯 Automatic Matching</h3>
            <p>Students are automatically paired with advisers whose expertise aligns with their queries, ensuring relevant and effective support.</p>
        </div>
        <div class="feature">
            <h3>📅 Real-Time Calendar</h3>
            <p>View adviser availability in real-time and book meetings at times that suit both parties, reducing scheduling conflicts.</p>
        </div>
        <div class="feature">
            <h3>🔔 Smart Notifications</h3>
            <p>Receive instant notifications for booking confirmations, updates, and reminders to stay on top of your meetings.</p>
        </div>
        <div class="feature">
            <h3>📊 Booking History</h3>
            <p>Access your complete booking history, track past meetings, and maintain continuity in your academic support journey.</p>
        </div>
        <div class="feature">
            <h3>🔄 Easy Rescheduling</h3>
            <p>Cancel or reschedule bookings with ease, giving you flexibility to manage your academic commitments effectively.</p>
        </div>
        <div class="feature">
            <h3>🔒 GDPR Compliant</h3>
            <p>Your data is protected with secure encryption and automatic deletion policies, ensuring full compliance with data protection standards.</p>
        </div>
    </div>
</x-layout>
