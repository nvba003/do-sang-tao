<div x-data="sidebarComponent({{ json_encode($menus) }})">

<div class="toggle-button-container fixed left-0 top-0 z-50">
    <button @click="showHideSidebar" class="flex items-center justify-between px-2 py-2 bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-300 ease-in-out">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="bi bi-list w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8M4 18h16" />
        </svg>
    </button>
</div>

<aside
       :class="{ 'w-80': !collapsed && !hideSidebar, 'w-10': collapsed && !hideSidebar, 'hidden': hideSidebar, 
        'md:relative': !collapsed || hideSidebar, 'relative': collapsed || hideSidebar }" 
       class="fixed inset-y-0 min-h-screen bg-gray-800 text-white fixed z-40 transform transition-width">
    <div :class="{'right-sidebar-expanded': !collapsed, 'right-sidebar-collapsed': collapsed}" class="sidebar-toggle-container fixed top-10">
        <button @click="toggleSidebar" class="toggle-sidebar absolute flex items-center justify-between px-2 py-2 bg-gray-700 opacity-100 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="bi bi-list w-4 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
            </svg>
        </button>
    </div>

    <nav>
        <div class="bg-blue-500 h-[40px]">
            <div class="flex justify-center">
                <span x-show="!collapsed" x-transition:enter="transition-opacity ease-linear duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="mt-1 text-lg">Đồ Sáng Tạo App</span>
            </div>
        </div>
        <div class="bg-gray-800 h-[30px]"></div>
        <div>
            <ul class="nav-list">
                @foreach ($menus as $menu)
                    <li class="menu-item relative"data-menu-id="{{ $menu->id }}" @mouseenter="enterMenu($event)" @mouseleave="leaveMenu($event)">
                        <button @click="setActiveMenu('{{ $menu->id }}')" :class="{ 'active-menu': isActiveMenu('{{ $menu->id }}') }"
                                class="menu-toggle flex justify-between items-center w-full px-2 py-2 text-left hover:bg-gray-700 focus:outline-none focus:bg-gray-700">
                            <span class="flex items-center">
                                {!! $menu->icon !!}
                                <span x-show="!collapsed && (activeMenu === '{{ $menu->id }}' || sidebarExpanded)" class="ml-2 menu-name">{{ $menu->name }}</span>
                            </span>
                        </button>
                        <div x-show="activeMenu === '{{ $menu->id }}' && !collapsed && sidebarExpanded" class="flex flex-col pl-4 bg-gray-800 w-full">
                            @foreach ($menu->children as $child)
                                    <a href="{{ url($child->url) }}" @click="handleSubmenuClick('{{ $child->id }}', '{{ $menu->id }}', '{{ url($child->url) }}', $event)" 
                                   :class="{ 'active-submenu': isActiveSubmenu('{{ $child->id }}') }"
                                   class="block px-4 py-2 hover:bg-gray-600 text-white cursor-pointer no-underline">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
    </nav>

    <div x-show="showModal && collapsed" class="submenuModal fixed bg-gray-800 text-white pb-2 z-50"
        :style="'top: ' + modalTop + '; left: ' + modalLeft"
        @mouseenter="mouseEnterModal" @mouseleave="mouseLeaveModal" x-transition>
        <ul>
            <li class="text-white bg-gray-600 font-bold p-2 mb-2 shadow w-[200px]" x-text="submenuTitle"></li>
            <template x-for="item in submenuItems" :key="item.id">
                <a :href="item.url" :class="item.class" @click="handleSubmenuClick(item.id, item.menuId, item.url, $event)" x-text="item.name" class="cursor-pointer submenu-item"></a>
            </template>
        </ul>
    </div>

</aside>
</div>

<script>
function sidebarComponent(menusData) {
    return {
        menus: menusData,
        collapsed: sessionStorage.getItem('sidebarCollapsed') === 'true' || false,
        activeMenu: sessionStorage.getItem('activeMenu') || null,
        sidebarExpanded: !this.collapsed,
        showModal: false,
        submenuTitle: '',
        submenuItems: [],
        modalTop: '0px',
        modalLeft: '0px',
        hoveringMenuItem: false,
        hideSidebar: false,
        content: document.getElementById('content'),

        updateContentWidth() {
            const content = document.getElementById('content');
            if (this.collapsed && !this.hideSidebar) {
                content.style.width = 'calc(100% - 40px)';  // Thu nhỏ sidebar
            } else {
                content.style.width = '100%';  // Mở rộng sidebar hoặc sidebar bị ẩn
            }
        },

        showHideSidebar() {
            this.hideSidebar = !this.hideSidebar;
            this.updateContentWidth();
        },

        toggleSidebar() {
            this.collapsed = !this.collapsed;
            sessionStorage.setItem('sidebarCollapsed', this.collapsed);
            this.sidebarExpanded = !this.collapsed;
            this.updateContentWidth();
        },
        setActiveMenu(menuId) {
            this.activeMenu = this.activeMenu === menuId ? null : menuId;
            // sessionStorage.setItem('activeMenu', this.activeMenu);
        },
        isActiveMenu(menuId) {
            return this.activeMenu === menuId;
        },
        handleSubmenuClick(submenuId, menuId, url, event) {
            event.preventDefault();
            // console.log(menuId);
            sessionStorage.setItem('activeSubmenu', submenuId);
            sessionStorage.setItem('activeMenu', menuId);
            // window.location.href = url;
            window.location.href = new URL(url, window.location.origin).href;
        },
        isActiveSubmenu(submenuId) {
            return sessionStorage.getItem('activeSubmenu') === submenuId.toString();
        },
        enterMenu(event) {
            this.hoveringMenuItem = true;
            clearTimeout(this.modalTimeout);
            const menuId = parseInt(event.currentTarget.getAttribute('data-menu-id'), 10);
            const menu = this.menus.find(m => m.id === menuId);
            if (menu && this.collapsed) {
                this.submenuTitle = menu.name;
                this.submenuItems = menu.children.map(child => ({
                    id: child.id,
                    name: child.name,
                    url: new URL(child.url, window.location.origin).href, 
                    // url: child.url,
                    menuId: menu.id,
                    class: parseInt(sessionStorage.getItem('activeSubmenu'), 10) === child.id ? 'active-submenu-item' : 'submenu-item'
                }));
                const rect = event.currentTarget.getBoundingClientRect();
                this.modalTop = `${rect.top}px`;
                this.modalLeft = `${rect.right}px`;
                this.showModal = true;
            }
        },
        leaveMenu(event) {
            this.hoveringMenuItem = false;
            // Đặt timeout để cho phép chuyển đổi giữa các menu item
            this.modalTimeout = setTimeout(() => {
                if (!this.hoveringMenuItem) {
                    this.showModal = false;
                }
            }, 300);
        },

        mouseEnterModal() {
            this.hoveringMenuItem = true;
            clearTimeout(this.modalTimeout);
        },

        mouseLeaveModal() {
            this.hoveringMenuItem = false;
            this.modalTimeout = setTimeout(() => {
                if (!this.hoveringMenuItem) {
                    this.showModal = false;
                }
            }, 300);
        }

    };
}
</script>