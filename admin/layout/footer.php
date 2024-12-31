
</main>
    </div>

    <script>

        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m3.343-5.657L5.929 5.93M18.071 5.93l1.414 1.414M6.343 16.657L5.93 18.07M18.07 18.071l1.414-1.414');
        }

        themeToggle.addEventListener('click', () => {
            htmlElement.classList.toggle('dark');

            if (htmlElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m3.343-5.657L5.929 5.93M18.071 5.93l1.414 1.414M6.343 16.657L5.93 18.07M18.07 18.071l1.414-1.414');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
            }
        });

        const userProfileToggle = document.getElementById('userProfileToggle');
        const userProfileMenu = document.getElementById('userProfileMenu');

        userProfileToggle.addEventListener('click', () => {
            userProfileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!userProfileToggle.contains(event.target) && !userProfileMenu.contains(event.target)) {
                userProfileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>