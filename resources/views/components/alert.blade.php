@if ($message)
    <div id="success-alert" 
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 
               flex items-center p-4 text-green-800 border border-green-300 
               rounded-lg bg-green-100 dark:bg-green-200 dark:text-green-900 
               shadow-lg opacity-100 transition-all duration-500 ease-in-out"
        role="alert">
        
        <svg class="w-5 h-5 text-green-700 dark:text-green-900" 
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
            fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" 
                d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-3a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V7Zm-1 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" 
                clip-rule="evenodd"/>
        </svg>

        <span class="ml-3 text-sm font-medium">{{ $message }}</span>
    </div>

    <script>
        // Smooth fade-out animation after 3 seconds
        setTimeout(function () {
            let alertBox = document.getElementById('success-alert');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s, transform 0.5s ease-in-out";
                alertBox.style.opacity = "0";
                alertBox.style.transform = "translate(-50%, -20px)"; // Moves up while fading out
               // just hide with display: none
                setTimeout(() => alertBox.style.display = "none", 500);
            }
        }, 3000);
    </script>
@endif
