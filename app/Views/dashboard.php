<div class="d-flex">
    <nav id="sidebar">
        <div class="p-4"><h4 class="text-center text-white m-0">Mi Empresa</h4></div>
        <hr class="mx-3 opacity-25">
        <ul class="nav flex-column" id="sidebar-dinamico"></ul>
        <div class="px-3 pb-4 mt-auto">
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm w-100">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-2">
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 fw-semibold text-dark"><?= session()->get('nombre') ?></span>
                <img src="<?= session()->get('foto') ?>" class="rounded-circle user-img" width="40" height="40" 
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= session()->get('nombre') ?>';">
            </div>
        </nav>
        <div class="container-fluid p-4" id="mainWrapper">
            <h3>Bienvenido</h3>
        </div>
    </div>
</div>

<script>
    const PERFIL_ID = "<?= session()->get('idPerfil') ?>";

    async function renderizarMenu() {
        const response = await fetch(`<?= base_url('menu/obtenerMenu') ?>?idPerfil=${PERFIL_ID}`);
        const datos = await response.json();
        let html = '';
        datos.forEach(p => {
            html += `
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="cargarModulo('${p.strNombreModulo}')">
                        <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i>
                        <span>${p.strNombreModulo}</span>
                    </a>
                </li>`;
        });
        document.getElementById('sidebar-dinamico').innerHTML = html;
    }

    async function cargarModulo(nombre) {
        const url = nombre.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '-').toLowerCase();
        const res = await fetch(`<?= base_url() ?>${url}/vista`);
        document.getElementById('mainWrapper').innerHTML = await res.text();
    }

    document.addEventListener('DOMContentLoaded', renderizarMenu);
</script>