<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Absensi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        .typing-effect::after {
            content: '_';
            animation: typing 1s infinite;
        }

        @keyframes typing {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-shadow {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Modal styles */
        #modal-overlay {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        #modal-content {
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Tambahan animasi untuk elemen modal */
        .modal-enter {
            animation: modalEnter 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalEnter {
            0% {
                opacity: 0;
                transform: scale(0.5) translateY(-50px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
</head>

<body
    class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
        <div
            class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
            <div class="text-center mb-8">
                <h1 class="mb-4 font-medium text-3xl text-[#1b1b18] dark:text-[#EDEDEC]">Sistem Absensi RFID</h1>
                <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A] text-base">Silakan scan kartu RFID Anda untuk mencatat
                    kehadiran hari ini.</p>

                <!-- Petunjuk Scan -->
                <div
                    class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <div class="flex items-center justify-center mb-3">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Cara Scan Kartu</h3>
                    </div>
                    <ol class="text-left text-blue-700 dark:text-blue-300 space-y-2">
                        <li class="flex items-start">
                            <span
                                class="inline-block w-6 h-6 bg-blue-500 text-white rounded-full text-center text-sm font-bold mr-3 mt-0.5">1</span>
                            <span>Dekatkan kartu RFID Anda ke papan reader</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="inline-block w-6 h-6 bg-blue-500 text-white rounded-full text-center text-sm font-bold mr-3 mt-0.5">2</span>
                            <span>Tunggu hingga terdengar bunyi "beep" dari reader</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="inline-block w-6 h-6 bg-blue-500 text-white rounded-full text-center text-sm font-bold mr-3 mt-0.5">3</span>
                            <span>Sistem akan otomatis mencatat kehadiran Anda</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Modal Overlay untuk Pesan -->
            <div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center"
                style="display: none;">
                <div id="modal-content"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 mx-4 max-w-md w-full transform scale-95 transition-all duration-300">
                    <!-- Modal content akan diisi oleh JavaScript -->
                </div>
            </div>

            <!-- Hidden data untuk JavaScript -->
            @if(session('success'))
                <div id="success-message" class="hidden" data-message="{{ session('success') }}"></div>
            @endif

            @if(session('error'))
                <div id="error-message" class="hidden" data-message="{{ session('error') }}"></div>
            @endif

            @if($errors->any())
                <div id="validation-errors" class="hidden" data-errors='@json($errors->all())'></div>
            @endif

            <form action="{{ route('attendance.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Status Indicator -->
                <div id="status-indicator"
                    class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <div class="text-3xl mb-2">📱</div>
                    <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">Siap untuk Scan</div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">Tempelkan kartu RFID ke reader</div>
                </div>

                <div>
                    <label for="rfid" class="block text-lg font-semibold mb-3 text-[#1b1b18] dark:text-[#EDEDEC]">
                        🔍 Input RFID
                    </label>
                    <input type="text" name="rfid" id="rfid" required autofocus
                        placeholder="Data RFID akan muncul di sini secara otomatis..."
                        class="w-full px-6 py-4 text-lg border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg focus:outline-none focus:ring-4 focus:ring-[#f53003]/20 focus:border-[#f53003] transition-all duration-200 bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] dark:placeholder-[#A1A09A]">
                </div>

                <input type="hidden" name="scanned_at" value="{{ now() }}">

                <!-- Manual Submit Button (tersembunyi, hanya untuk debugging) -->
                <div class="text-center" style="display: none;" id="manual-submit">
                    <button type="submit"
                        class="px-8 py-3 bg-[#f53003] hover:bg-[#d42a02] text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-[#f53003]/20">
                        Submit Manual
                    </button>
                </div>
            </form>

            <!-- Login Admin Button -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Kembali ke halaman dashboard
                    </p>
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-purple-500/20 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rfidInput = document.getElementById('rfid');
            const form = document.querySelector('form');
            const statusIndicator = document.getElementById('status-indicator');
            const modalOverlay = document.getElementById('modal-overlay');
            const modalContent = document.getElementById('modal-content');

            // Fungsi untuk menampilkan modal
            function showModal(type, message, errors = null) {
                let icon, bgColor, textColor, title;

                switch (type) {
                    case 'success':
                        icon = '✅';
                        bgColor = 'bg-gradient-to-br from-green-400 to-green-600';
                        textColor = 'text-white';
                        title = 'Berhasil!';
                        break;
                    case 'error':
                        icon = '❌';
                        bgColor = 'bg-gradient-to-br from-red-400 to-red-600';
                        textColor = 'text-white';
                        title = 'Error!';
                        break;
                    case 'validation':
                        icon = '⚠️';
                        bgColor = 'bg-gradient-to-br from-yellow-400 to-orange-500';
                        textColor = 'text-white';
                        title = 'Perhatian!';
                        break;
                }

                let content = `
                        <div class="${bgColor} ${textColor} p-6 rounded-xl text-center">
                            <div class="text-6xl mb-4">${icon}</div>
                            <h3 class="text-2xl font-bold mb-3">${title}</h3>
                            <div class="text-lg">
                                ${message ? `<p class="mb-2">${message}</p>` : ''}
                                ${errors ? `
                                    <div class="text-left mt-4">
                                        <p class="font-semibold mb-2">Detail kesalahan:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            ${errors.map(error => `<li class="text-sm">${error}</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;

                modalContent.innerHTML = content;
                modalOverlay.style.display = 'flex';

                // Animasi masuk
                setTimeout(() => {
                    modalContent.style.transform = 'scale(1)';
                }, 10);

                // Auto close setelah 1 detik
                setTimeout(() => {
                    closeModal();
                }, 1000);
            }

            // Fungsi untuk menutup modal
            function closeModal() {
                modalContent.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    modalOverlay.style.display = 'none';
                }, 300);
            }

            // Cek dan tampilkan pesan saat halaman dimuat
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const validationErrors = document.getElementById('validation-errors');

            if (successMessage) {
                showModal('success', successMessage.dataset.message);
            }

            if (errorMessage) {
                showModal('error', errorMessage.dataset.message);
            }

            if (validationErrors) {
                const errors = JSON.parse(validationErrors.dataset.errors);
                showModal('validation', 'Terjadi kesalahan validasi:', errors);
            }

            // Tutup modal ketika overlay diklik
            modalOverlay.addEventListener('click', function (e) {
                if (e.target === modalOverlay) {
                    closeModal();
                }
            });

            // Focus pada input RFID saat halaman dimuat
            rfidInput.focus();

            // Update status indicator saat input berubah
            rfidInput.addEventListener('input', function () {
                if (rfidInput.value.trim()) {
                    statusIndicator.innerHTML = `
                            <div class="text-3xl mb-2">✅</div>
                            <div class="text-lg font-semibold text-green-600 dark:text-green-400">Kartu Terdeteksi!</div>
                            <div class="text-sm text-green-500 dark:text-green-500">Memproses data...</div>
                        `;
                    statusIndicator.className = 'text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-2 border-green-300 dark:border-green-600';
                } else {
                    statusIndicator.innerHTML = `
                            <div class="text-3xl mb-2">📱</div>
                            <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">Siap untuk Scan</div>
                            <div class="text-sm text-gray-500 dark:text-gray-500">Tempelkan kartu RFID ke reader</div>
                        `;
                    statusIndicator.className = 'text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600';
                }
            });

            // Auto-submit form ketika RFID selesai di-scan
            rfidInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    // Pastikan RFID sudah diisi
                    if (!rfidInput.value.trim()) {
                        alert('Silakan scan kartu RFID terlebih dahulu!');
                        return;
                    }

                    // Update status indicator untuk processing
                    statusIndicator.innerHTML = `
                            <div class="text-3xl mb-2">⏳</div>
                            <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">Memproses Absensi...</div>
                            <div class="text-sm text-blue-500 dark:text-blue-500">Mohon tunggu sebentar</div>
                        `;
                    statusIndicator.className = 'text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-2 border-blue-300 dark:border-blue-600';

                    // Submit form
                    form.submit();
                }
            });

            // Tidak perlu auto-submit setelah delay, cukup submit via Enter

            // Reset form setelah submit
            form.addEventListener('submit', function () {
                setTimeout(function () {
                    rfidInput.value = '';
                    rfidInput.focus();
                    form.classList.remove('submitting');

                    // Reset status indicator
                    statusIndicator.innerHTML = `
                            <div class="text-3xl mb-2">📱</div>
                            <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">Siap untuk Scan</div>
                            <div class="text-sm text-gray-500 dark:text-gray-500">Tempelkan kartu RFID ke reader</div>
                        `;
                    statusIndicator.className = 'text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600';
                }, 3000);
            });

            // Double click untuk enable manual submit (debugging)
            let clickCount = 0;
            rfidInput.addEventListener('click', function () {
                clickCount++;
                if (clickCount === 5) {
                    document.getElementById('manual-submit').style.display = 'block';
                    rfidInput.removeAttribute('readonly');
                    rfidInput.placeholder = 'Masukkan RFID manual untuk testing...';
                    clickCount = 0;
                }
                setTimeout(() => clickCount = 0, 2000);
            });
        });
    </script>
</body>

</html>