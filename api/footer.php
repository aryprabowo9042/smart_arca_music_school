<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">© 2024 Smart Arca Music School. All rights reserved.</span>
    </div>
</footer>

<?php 
/** * LOGIKA TAMPILAN CHATBOT
 * Chatbot hanya akan muncul jika user berada di halaman index.php atau root domain.
 */
$currentPage = basename($_SERVER['SCRIPT_NAME']);
if ($currentPage == 'index.php' || $currentPage == ''): 
?>

<div id="ai-chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <button id="chat-open-btn" style="width: 60px; height: 60px; border-radius: 50%; background: #2563eb; color: white; border: none; cursor: pointer; font-size: 28px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        💬
    </button>

    <div id="chat-window" style="display: none; width: 330px; height: 480px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); flex-direction: column; overflow: hidden; position: absolute; bottom: 80px; right: 0;">
        
        <div style="background: #2563eb; color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></div>
                <span>Smart Arca AI Assistant</span>
            </div>
            <button id="chat-close-btn" style="background:none; border:none; color:white; cursor:pointer; font-size: 20px;">✕</button>
        </div>

        <div id="chat-messages-container" style="flex: 1; padding: 15px; overflow-y: auto; background: #f1f5f9; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
            <div style="background: #e2e8f0; color: #1e293b; padding: 10px 14px; border-radius: 15px 15px 15px 2px; align-self: flex-start; max-width: 85%; font-size: 14px; line-height: 1.4; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                Halo! Selamat datang di Smart Arca Music School. Ada yang bisa saya bantu terkait informasi kursus atau pendaftaran?
            </div>
        </div>

        <div style="padding: 15px; border-top: 1px solid #e2e8f0; background: white; display: flex; gap: 8px;">
            <input type="text" id="user-input-field" placeholder="Ketik pesan di sini..." style="flex: 1; border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; outline: none; font-size: 14px;">
            <button id="send-message-btn" style="background: #2563eb; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                ➤
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const openBtn = document.getElementById('chat-open-btn');
    const closeBtn = document.getElementById('chat-close-btn');
    const chatWindow = document.getElementById('chat-window');
    const msgContainer = document.getElementById('chat-messages-container');
    const inputField = document.getElementById('user-input-field');
    const sendBtn = document.getElementById('send-message-btn');

    // Buka & Tutup Chat
    openBtn.onclick = () => { chatWindow.style.display = 'flex'; inputField.focus(); };
    closeBtn.onclick = () => { chatWindow.style.display = 'none'; };

    async function processChat() {
        const text = inputField.value.trim();
        if (!text) return;

        // Tampilkan pesan user di layar
        msgContainer.innerHTML += `
            <div style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 15px 15px 2px 15px; align-self: flex-end; max-width: 85%; font-size: 14px; line-height: 1.4; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                ${text}
            </div>`;
        
        inputField.value = '';
        msgContainer.scrollTop = msgContainer.scrollHeight;

        // Tampilkan indikator loading (titik-titik)
        const loadingId = "loading-" + Date.now();
        msgContainer.innerHTML += `
            <div id="${loadingId}" style="background: #e2e8f0; color: #64748b; padding: 10px 14px; border-radius: 15px 15px 15px 2px; align-self: flex-start; font-size: 14px; font-style: italic;">
                Mengetik...
            </div>`;
        msgContainer.scrollTop = msgContainer.scrollHeight;

        try {
            // Mengirim data ke file chat_process.php
            const response = await fetch('chat_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pesan: text })
            });
            
            const data = await response.json();
            const aiResponse = data.jawaban || "Maaf, saya sedang kesulitan memproses pesan Anda.";

            // Hapus loading dan ganti dengan jawaban AI
            const loadingElement = document.getElementById(loadingId);
            loadingElement.style.fontStyle = "normal";
            loadingElement.style.color = "#1e293b";
            loadingElement.innerText = aiResponse;

        } catch (error) {
            document.getElementById(loadingId).innerText = "Maaf, terjadi kesalahan koneksi ke server.";
        }
        
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    // Event Klik Tombol Kirim
    sendBtn.onclick = processChat;

    // Event Tekan Enter di Keyboard
    inputField.onkeypress = (e) => { if (e.key === 'Enter') processChat(); };
})();
</script>

<?php endif; // Akhir dari pengecekan halaman ?>
