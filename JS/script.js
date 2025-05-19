function appData() {
      return {
        currentPage: 'home',
        mobileMenuOpen: false,
        showPhoneDetail: false,
        showComparisonAlert: false,
        phoneDetail: {},
        searchQuery: '',
        filters: {
          brand: '',
          price: '',
          ram: ''
        },
        selectedPhones: [],
        
        // Smartphone Data
        phones: [
          {
            id: 1,
            name: "iPhone 14 Pro Max",
            brand: "Apple",
            price: 19999000,
            rating: 4.8,
            image: "/api/placeholder/300/300",
            specs: {
              os: "iOS 16",
              processor: "Apple A16 Bionic",
              ram: 6,
              storage: 256,
              display: "6.7 inch, Super Retina XDR OLED, 120Hz",
              mainCamera: "48MP (Wide) + 12MP (Ultra-wide) + 12MP (Telephoto)",
              selfieCamera: "12MP TrueDepth",
              battery: 4323,
              charging: 27,
              dimensions: "160.7 x 77.6 x 7.9 mm",
              weight: 240,
              features: ["Face ID", "MagSafe Charging", "Dynamic Island", "Always-on Display", "Emergency SOS via satellite"]
            }
          },
          {
            id: 2,
            name: "Samsung Galaxy S22 Ultra",
            brand: "Samsung",
            price: 17999000,
            rating: 4.7,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, One UI 4.1",
              processor: "Snapdragon 8 Gen 1",
              ram: 12,
              storage: 256,
              display: "6.8 inch, Dynamic AMOLED 2X, 120Hz",
              mainCamera: "108MP (Wide) + 12MP (Ultra-wide) + 10MP (Telephoto) + 10MP (Periscope)",
              selfieCamera: "40MP",
              battery: 5000,
              charging: 45,
              dimensions: "163.3 x 77.9 x 8.9 mm",
              weight: 228,
              features: ["S Pen Support", "IP68 water resistance", "Wireless PowerShare", "Samsung DeX", "Ultra Wideband (UWB)"]
            }
          },
          {
            id: 3,
            name: "Google Pixel 7 Pro",
            brand: "Google",
            price: 13999000,
            rating: 4.6,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 13",
              processor: "Google Tensor G2",
              ram: 12,
              storage: 128,
              display: "6.7 inch, LTPO AMOLED, 120Hz",
              mainCamera: "50MP (Wide) + 12MP (Ultra-wide) + 48MP (Telephoto)",
              selfieCamera: "10.8MP",
              battery: 5000,
              charging: 30,
              dimensions: "162.9 x 76.6 x 8.9 mm",
              weight: 212,
              features: ["Face Unlock", "Titan M2 security", "Magic Eraser", "Photo Unblur", "Google Assistant"]
            }
          },
          {
            id: 4,
            name: "Xiaomi 12T Pro",
            brand: "Xiaomi",
            price: 9999000,
            rating: 4.5,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, MIUI 13",
              processor: "Snapdragon 8+ Gen 1",
              ram: 8,
              storage: 256,
              display: "6.67 inch, AMOLED, 120Hz",
              mainCamera: "200MP (Wide) + 8MP (Ultra-wide) + 2MP (Macro)",
              selfieCamera: "20MP",
              battery: 5000,
              charging: 120,
              dimensions: "163.1 x 75.9 x 8.6 mm",
              weight: 205,
              features: ["Harman Kardon speakers", "Dolby Vision", "Dolby Atmos", "NFC", "IR Blaster"]
            }
          },
          {
            id: 5,
            name: "OPPO Find X5 Pro",
            brand: "OPPO",
            price: 15999000,
            rating: 4.5,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, ColorOS 12.1",
              processor: "Snapdragon 8 Gen 1",
              ram: 12,
              storage: 256,
              display: "6.7 inch, LTPO2 AMOLED, 120Hz",
              mainCamera: "50MP (Wide) + 50MP (Ultra-wide) + 13MP (Telephoto)",
              selfieCamera: "32MP",
              battery: 5000,
              charging: 80,
              dimensions: "163.7 x 73.9 x 8.5 mm",
              weight: 218,
              features: ["Hasselblad Camera", "MariSilicon X Imaging NPU", "IP68 water resistance", "Ceramic back", "Wireless charging"]
            }
          },
          {
            id: 6,
            name: "Vivo X80 Pro",
            brand: "Vivo",
            price: 14999000,
            rating: 4.4,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, Funtouch OS 12",
              processor: "Snapdragon 8 Gen 1",
              ram: 12,
              storage: 256,
              display: "6.78 inch, LTPO3 AMOLED, 120Hz",
              mainCamera: "50MP (Wide) + 48MP (Ultra-wide) + 12MP (Portrait) + 8MP (Periscope)",
              selfieCamera: "32MP",
              battery: 4700,
              charging: 80,
              dimensions: "164.6 x 75.3 x 9.1 mm",
              weight: 219,
              features: ["ZEISS Optics", "V1+ Imaging Chip", "3D Ultrasonic Fingerprint", "IP68 water resistance", "Wireless charging"]
            }
          },
          {
            id: 7,
            name: "Samsung Galaxy A53 5G",
            brand: "Samsung",
            price: 5999000,
            rating: 4.2,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, One UI 4.1",
              processor: "Exynos 1280",
              ram: 8,
              storage: 128,
              display: "6.5 inch, Super AMOLED, 120Hz",
              mainCamera: "64MP (Wide) + 12MP (Ultra-wide) + 5MP (Macro) + 5MP (Depth)",
              selfieCamera: "32MP",
              battery: 5000,
              charging: 25,
              dimensions: "159.6 x 74.8 x 8.1 mm",
              weight: 189,
              features: ["IP67 water resistance", "Stereo speakers", "microSD card slot", "5G connectivity", "Samsung Knox security"]
            }
          },
          {
            id: 8,
            name: "Xiaomi Redmi Note 11 Pro",
            brand: "Xiaomi",
            price: 3999000,
            rating: 4.0,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 11, MIUI 13",
              processor: "MediaTek Helio G96",
              ram: 6,
              storage: 128,
              display: "6.67 inch, AMOLED, 120Hz",
              mainCamera: "108MP (Wide) + 8MP (Ultra-wide) + 2MP (Macro) + 2MP (Depth)",
              selfieCamera: "16MP",
              battery: 5000,
              charging: 67,
              dimensions: "164.2 x 76.1 x 8.1 mm",
              weight: 202,
              features: ["Stereo speakers", "IR Blaster", "microSD card slot", "NFC", "3.5mm headphone jack"]
            }
          },
          {
            id: 9,
            name: "iPhone SE (2022)",
            brand: "Apple",
            price: 7999000,
            rating: 4.1,
            image: "/api/placeholder/300/300",
            specs: {
              os: "iOS 15",
              processor: "Apple A15 Bionic",
              ram: 4,
              storage: 64,
              display: "4.7 inch, Retina IPS LCD, 60Hz",
              mainCamera: "12MP (Wide)",
              selfieCamera: "7MP",
              battery: 2018,
              charging: 20,
              dimensions: "138.4 x 67.3 x 7.3 mm",
              weight: 144,
              features: ["Touch ID", "IP67 water resistance", "Wireless charging", "MagSafe compatible", "5G connectivity"]
            }
          },
          {
            id: 10,
            name: "Google Pixel 6a",
            brand: "Google",
            price: 6999000,
            rating: 4.3,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12",
              processor: "Google Tensor",
              ram: 6,
              storage: 128,
              display: "6.1 inch, OLED, 60Hz",
              mainCamera: "12.2MP (Wide) + 12MP (Ultra-wide)",
              selfieCamera: "8MP",
              battery: 4410,
              charging: 18,
              dimensions: "152.2 x 71.8 x 8.9 mm",
              weight: 178,
              features: ["Titan M2 security", "In-display fingerprint sensor", "IP67 water resistance", "Google Assistant", "5G connectivity"]
            }
          },
          {
            id: 11,
            name: "OPPO Reno 8 Pro",
            brand: "OPPO",
            price: 8499000,
            rating: 4.2,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, ColorOS 12.1",
              processor: "MediaTek Dimensity 8100",
              ram: 12,
              storage: 256,
              display: "6.7 inch, AMOLED, 120Hz",
              mainCamera: "50MP (Wide) + 8MP (Ultra-wide) + 2MP (Macro)",
              selfieCamera: "32MP",
              battery: 4500,
              charging: 80,
              dimensions: "161.2 x 74.2 x 7.3 mm",
              weight: 183,
              features: ["MariSilicon X Imaging NPU", "In-display fingerprint sensor", "Stereo speakers", "NFC", "5G connectivity"]
            }
          },
          {
            id: 12,
            name: "Vivo V25 Pro",
            brand: "Vivo",
            price: 7499000,
            rating: 4.0,
            image: "/api/placeholder/300/300",
            specs: {
              os: "Android 12, Funtouch OS 12",
              processor: "MediaTek Dimensity 1300",
              ram: 8,
              storage: 128,
              display: "6.56 inch, AMOLED, 120Hz",
              mainCamera: "64MP (Wide) + 8MP (Ultra-wide) + 2MP (Macro)",
              selfieCamera: "32MP",
              battery: 4830,
              charging: 66,
              dimensions: "158.9 x 73.5 x 8.6 mm",
              weight: 190,
              features: ["Color changing back panel", "In-display fingerprint sensor", "OIS camera", "Extended RAM", "5G connectivity"]
            }
          }
        ],
        
        // Computed properties
        get activeFilters() {
          return {
            brand: this.filters.brand,
            price: this.filters.price,
            ram: this.filters.ram
          };
        },
        
        get hasActiveFilters() {
          return Object.values(this.filters).some(filter => filter !== '');
        },
        
        get filteredPhones() {
          return this.phones.filter(phone => {
            // Search query filter
            if (this.searchQuery && !phone.name.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
                !phone.brand.toLowerCase().includes(this.searchQuery.toLowerCase())) {
              return false;
            }
            
            // Brand filter
            if (this.filters.brand && phone.brand !== this.filters.brand) {
              return false;
            }
            
            // Price filter
            if (this.filters.price) {
              if (this.filters.price === 'budget' && phone.price >= 3000000) return false;
              if (this.filters.price === 'midrange' && (phone.price < 3000000 || phone.price >= 7000000)) return false;
              if (this.filters.price === 'premium' && (phone.price < 7000000 || phone.price >= 12000000)) return false;
              if (this.filters.price === 'flagship' && phone.price < 12000000) return false;
            }
            
            // RAM filter
            if (this.filters.ram) {
              const ramValue = parseInt(this.filters.ram);
              if (this.filters.ram === '12' && phone.specs.ram < 12) return false;
              else if (phone.specs.ram !== ramValue) return false;
            }
            
            return true;
          });
        },
        
        // Methods
        formatPrice(price) {
          return 'Rp ' + price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        formatFilterLabel(key, value) {
          if (key === 'brand') return value;
          if (key === 'price') {
            if (value === 'budget') return '< Rp 3 Juta';
            if (value === 'midrange') return 'Rp 3 - 7 Juta';
            if (value === 'premium') return 'Rp 7 - 12 Juta';
            if (value === 'flagship') return '> Rp 12 Juta';
          }
          if (key === 'ram') return `RAM ${value}GB`;
          return value;
        },
        
        clearFilter(key) {
          this.filters[key] = '';
        },
        
        clearAllFilters() {
          this.filters.brand = '';
          this.filters.price = '';
          this.filters.ram = '';
          this.searchQuery = '';
        },
        
        togglePhoneSelection(phoneId) {
          const index = this.selectedPhones.indexOf(phoneId);
          if (index === -1) {
            if (this.selectedPhones.length < 4) {
              this.selectedPhones.push(phoneId);
            } else {
              alert('Maksimal 4 perangkat yang dapat dibandingkan secara bersamaan');
            }
          } else {
            this.selectedPhones.splice(index, 1);
          }
        },
        
        isPhoneSelected(phoneId) {
          return this.selectedPhones.includes(phoneId);
        },
        
        removePhoneFromComparison(phoneId) {
          const index = this.selectedPhones.indexOf(phoneId);
          if (index !== -1) {
            this.selectedPhones.splice(index, 1);
          }
        },
        
        getBestInCategory(phoneIds, category) {
          let bestPhoneId = null;
          let bestValue = null;
          
          for (const phoneId of phoneIds) {
            const phone = this.phones.find(p => p.id === phoneId);
            
            if (!phone) continue;
            
            let value;
            switch (category) {
              case 'ram':
              case 'storage':
              case 'battery':
              case 'charging':
                value = phone.specs[category];
                break;
              case 'processor':
                // Simplified processor comparison (just a placeholder)
                value = phone.specs.processor.includes('A16') ? 10 :
                       phone.specs.processor.includes('8 Gen 1') ? 9 :
                       phone.specs.processor.includes('Tensor') ? 8 :
                       phone.specs.processor.includes('Dimensity') ? 7 :
                       phone.specs.processor.includes('Helio') ? 6 :
                       phone.specs.processor.includes('Exynos') ? 5 : 0;
                break;
              case 'mainCamera':
                // Simple comparison based on primary camera MP
                value = parseInt(phone.specs.mainCamera.match(/\d+/)[0]);
                break;
              case 'selfieCamera':
                // Simple comparison based on selfie camera MP
                value = parseInt(phone.specs.selfieCamera.match(/\d+/)[0]);
                break;
              case 'display':
                // Simple comparison giving priority to higher refresh rate
                value = phone.specs.display.includes('120Hz') ? 3 :
                       phone.specs.display.includes('90Hz') ? 2 :
                       phone.specs.display.includes('60Hz') ? 1 : 0;
                break;
              case 'os':
                // Give some arbitrary value to compare OS
                value = phone.specs.os.includes('iOS') ? 2 :
                       phone.specs.os.includes('Android 13') ? 4 :
                       phone.specs.os.includes('Android 12') ? 3 :
                       phone.specs.os.includes('Android 11') ? 2 : 1;
                break;
              default:
                value = 0;
            }
            
            if (bestValue === null || value > bestValue) {
              bestValue = value;
              bestPhoneId = phone.id;
            }
          }
          
          return bestPhoneId;
        }
      };
    }