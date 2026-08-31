<!-- BANNER DE COOKIES (LGPD) -->
<div id="cookieBanner" class="cookie-banner shadow-lg" style="display: none;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-cookie-bite fs-3 text-orange"></i>
            <p class="small mb-0 text-secondary" style="max-width: 550px; line-height: 1.4;">
                Nós utilizamos cookies essenciais para garantir que você tenha a melhor experiência no <strong>PontoCaixa</strong> e para manter sua sessão segura. Ao continuar navegando, você concorda com o uso de cookies.
            </p>
        </div>
        <button id="btnAceitarCookies" class="btn btn-sm btn-brand px-4 py-2">
            Aceitar e Continuar
        </button>
    </div>
</div>

<style>
    .cookie-banner {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 850px;
        background-color: #ffffff;
        border: 1px solid #ECE8E1;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        z-index: 9999;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cookieBanner = document.getElementById("cookieBanner");
        const btnAceitar = document.getElementById("btnAceitarCookies");

        // Verifica se o usuário já aceitou os cookies anteriormente
        if (!localStorage.getItem("cookies_aceitos")) {
            cookieBanner.style.display = "block";
        }

        // Grava no localStorage ao clicar
        btnAceitar.addEventListener("click", function () {
            localStorage.setItem("cookies_aceitos", "true");
            cookieBanner.style.display = "none";
        });
    });
</script>