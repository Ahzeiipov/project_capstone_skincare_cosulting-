let images = ['/project_capstone_skincare/GlowMatch-front/asset/image/card-image/card1.jpeg',
     '/project_capstone_skincare/GlowMatch-front/asset/image/moisirizer/pink.jpeg', 
     '/project_capstone_skincare/GlowMatch-front/asset/image/foam/Poremizing Deep Cleansing Foam.png', 
     '/project_capstone_skincare/GlowMatch-front/asset/image/sunscreen/Skin1004 Madagascar Centella Air-Fit Suncream Plus SPF 50+ PA++++.png'];
        let currentIndex = 0;

        function changeImage(imageSrc) {
            document.getElementById("displayedImage").src = imageSrc;
        }

        function prevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById("displayedImage").src = images[currentIndex];
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById("displayedImage").src = images[currentIndex];
        }

        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
        }