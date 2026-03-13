<template>
    <div>
        <!-- Mobile overlay -->
        <div v-if="showMobile"
            @click="$emit('close')"
            :style="{ position:'fixed', inset:0, background:'rgba(0,0,0,.5)', zIndex:40, backdropFilter:'blur(4px)' }">
        </div>

        <!-- Sidebar -->
        <aside :class="['ep-nav', isDark ? 'dark' : '']"
            :style="{
                position:'fixed', top:0, left:0, height:'100vh', width:'256px',
                zIndex:50, display:'flex', flexDirection:'column',
                transform: showMobile ? 'translateX(0)' : (isMobileScreen ? 'translateX(-100%)' : 'translateX(0)'),
                transition:'transform .3s cubic-bezier(.4,0,.2,1)'
            }">

            <component :is="'style'">
                @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');

                .ep-nav {
                    --crimson: #C0170F;
                    --orange:  #F05A00;
                    --amber:   #F9B233;
                    --navy:    #1D5C96;
                    --bg:      #FFFFFF;
                    --bg-muted:#F0EDE8;
                    --border:  #E8E2DA;
                    --text:    #1A1410;
                    --text-2:  #6B6560;
                    --text-3:  #9E9890;
                    background: var(--bg);
                    border-right: 1px solid var(--border);
                    box-shadow: 4px 0 24px rgba(0,0,0,.06);
                }
                .ep-nav.dark {
                    --bg:      #1A1714;
                    --bg-muted:#242018;
                    --border:  #2E2925;
                    --text:    #F0EDE8;
                    --text-2:  #A09890;
                    --text-3:  #605850;
                    box-shadow: 4px 0 24px rgba(0,0,0,.4);
                }

                /* Brand accent bar */
                .ep-nav-accent {
                    height: 3px;
                    background: linear-gradient(90deg, var(--crimson), var(--orange), var(--amber), var(--navy));
                    flex-shrink: 0;
                }

                /* Logo area */
                .ep-nav-logo {
                    padding: 20px 18px 16px;
                    border-bottom: 1px solid var(--border);
                    flex-shrink: 0;
                }

                /* Nav scroll area */
                .ep-nav-scroll {
                    flex: 1;
                    overflow-y: auto;
                    padding: 12px 10px;
                    scrollbar-width: thin;
                    scrollbar-color: var(--border) transparent;
                }
                .ep-nav-scroll::-webkit-scrollbar { width: 4px; }
                .ep-nav-scroll::-webkit-scrollbar-track { background: transparent; }
                .ep-nav-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

                /* Section label */
                .ep-nav-section {
                    font-size: 9px;
                    font-family: 'DM Mono', monospace;
                    letter-spacing: .2em;
                    color: var(--text-3);
                    text-transform: uppercase;
                    padding: 14px 10px 6px;
                    font-weight: 600;
                }

                /* Nav item */
                .ep-nav-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 9px 12px;
                    border-radius: 10px;
                    text-decoration: none;
                    font-size: 13px;
                    font-family: 'DM Sans', sans-serif;
                    font-weight: 500;
                    color: var(--text-2);
                    transition: all .18s;
                    cursor: pointer;
                    border: none;
                    background: transparent;
                    width: 100%;
                    text-align: left;
                    position: relative;
                    margin-bottom: 1px;
                }
                .ep-nav-item:hover {
                    background: var(--bg-muted);
                    color: var(--text);
                }
                .ep-nav-item.active {
                    background: var(--crimson);
                    color: #fff;
                    font-weight: 600;
                }
                .ep-nav-item.active .ep-nav-icon { color: #fff; }

                /* Active left accent bar */
                .ep-nav-item.active::before {
                    content: '';
                    position: absolute;
                    left: 0; top: 50%;
                    transform: translateY(-50%);
                    width: 3px; height: 60%;
                    background: rgba(255,255,255,.5);
                    border-radius: 0 2px 2px 0;
                }

                /* Icon wrapper — fixed size so all icons align */
                .ep-nav-icon {
                    width: 18px;
                    height: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    color: var(--text-3);
                    transition: color .18s;
                    overflow: hidden;
                }
                .ep-nav-icon svg {
                    width: 16px !important;
                    height: 16px !important;
                    flex-shrink: 0;
                }
                .ep-nav-item:hover .ep-nav-icon { color: var(--text-2); }

                /* Active dot */
                .ep-nav-dot {
                    margin-left: auto;
                    width: 5px; height: 5px;
                    border-radius: 50%;
                    background: rgba(255,255,255,.7);
                    flex-shrink: 0;
                }

                /* Logout special styling */
                .ep-nav-logout {
                    color: #ef4444;
                }
                .ep-nav-logout .ep-nav-icon { color: #ef4444; }
                .ep-nav-logout:hover {
                    background: rgba(239,68,68,.08);
                    color: #dc2626;
                }
                .ep-nav-logout:hover .ep-nav-icon { color: #dc2626; }
                .ep-nav.dark .ep-nav-logout { color: #f87171; }
                .ep-nav.dark .ep-nav-logout .ep-nav-icon { color: #f87171; }
                .ep-nav.dark .ep-nav-logout:hover { background: rgba(192,23,15,.15); color: #fca5a5; }

                /* User strip */
                .ep-nav-user {
                    padding: 14px 18px;
                    border-top: 1px solid var(--border);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex-shrink: 0;
                }
                .ep-nav-avatar {
                    width: 34px; height: 34px;
                    border-radius: 50%;
                    background: var(--crimson);
                    display: flex; align-items: center; justify-content: center;
                    font-size: 13px; font-weight: 700; color: #fff;
                    flex-shrink: 0;
                    position: relative;
                }
                .ep-nav-online {
                    position: absolute; bottom: 0; right: 0;
                    width: 9px; height: 9px;
                    border-radius: 50%;
                    background: #16a34a;
                    border: 2px solid var(--bg);
                }
            </component>

            <!-- Accent bar -->
            <div class="ep-nav-accent"></div>

            <!-- Logo -->
            <div class="ep-nav-logo">
                <div :style="{ display:'flex', alignItems:'center', gap:'10px', marginBottom:'6px' }">
                    <div :style="{ width:'36px', height:'36px', borderRadius:'10px', background:'linear-gradient(135deg, var(--crimson), var(--orange))', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'18px', flexShrink:0 }">🚪</div>
                    <div>
                        <div :style="{ fontFamily:'Playfair Display, serif', fontWeight:900, fontSize:'16px', lineHeight:1.1, color:'var(--text)' }">
                            Event <span :style="{ color:'var(--crimson)' }">Portal</span>
                        </div>
                        <div :style="{ fontFamily:'DM Mono, monospace', fontSize:'9px', letterSpacing:'.15em', color:'var(--text-3)', textTransform:'uppercase', marginTop:'2px' }">Admin Panel</div>
                    </div>
                </div>
            </div>

            <!-- Nav items -->
            <div class="ep-nav-scroll">

                <!-- Main -->
                <div class="ep-nav-section">Main</div>

                <Link :href="route('dashboard')" :class="['ep-nav-item', isActive('dashboard') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                        </svg>
                    </span>
                    Dashboard
                    <span v-if="isActive('dashboard')" class="ep-nav-dot"></span>
                </Link>

                <Link :href="route('users.index')" :class="['ep-nav-item', isActive('users') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    All Users
                    <span v-if="isActive('users')" class="ep-nav-dot"></span>
                </Link>

                <!-- Services -->
                <div class="ep-nav-section">Services</div>

                <Link :href="route('events.index')" :class="['ep-nav-item', isActive('events') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    My Events
                    <span v-if="isActive('events')" class="ep-nav-dot"></span>
                </Link>

                <Link :href="route('bookings.index')" :class="['ep-nav-item', isActive('bookings') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </span>
                    My Bookings
                    <span v-if="isActive('bookings')" class="ep-nav-dot"></span>
                </Link>

                <Link :href="route('reports.messages')" :class="['ep-nav-item', isActive('messages') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>
                    Messages Report
                    <span v-if="isActive('messages')" class="ep-nav-dot"></span>
                </Link>

                <Link :href="route('venues.index')" :class="['ep-nav-item', isActive('venues') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    Venues
                    <span v-if="isActive('venues')" class="ep-nav-dot"></span>
                </Link>

                <Link :href="route('vendors.index')" :class="['ep-nav-item', isActive('vendors') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </span>
                    Vendors
                    <span v-if="isActive('vendors')" class="ep-nav-dot"></span>
                </Link>

                <!-- Settings -->
                <div class="ep-nav-section">Settings</div>

                <Link :href="route('profile.edit')" :class="['ep-nav-item', isActive('profile') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    Profile
                    <span v-if="isActive('profile')" class="ep-nav-dot"></span>
                </Link>

                <Link v-if="$page.props.auth.user?.is_admin" :href="route('settings.index')" :class="['ep-nav-item', isActive('settings') ? 'active' : '']">
                    <span class="ep-nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/>
                        </svg>
                    </span>
                    Settings
                    <span v-if="isActive('settings')" class="ep-nav-dot"></span>
                </Link>

                <!-- Logout — icon fixed to same 16px as all others -->
                <div :style="{ borderTop: '1px solid var(--border)', marginTop:'8px', paddingTop:'8px' }">
                    <Link :href="route('logout')" method="post" as="button" class="ep-nav-item ep-nav-logout">
                        <span class="ep-nav-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </span>
                        Logout
                    </Link>
                </div>
            </div>

            <!-- User strip -->
            <div class="ep-nav-user">
                <div class="ep-nav-avatar">
                    {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                    <div class="ep-nav-online"></div>
                </div>
                <div :style="{ flex:1, minWidth:0 }">
                    <div :style="{ fontFamily:'DM Sans, sans-serif', fontSize:'13px', fontWeight:600, color:'var(--text)', whiteSpace:'nowrap', overflow:'hidden', textOverflow:'ellipsis' }">
                        {{ $page.props.auth.user?.name }}
                    </div>
                    <div :style="{ fontFamily:'DM Mono, monospace', fontSize:'10px', color:'var(--text-3)', whiteSpace:'nowrap', overflow:'hidden', textOverflow:'ellipsis' }">
                        {{ $page.props.auth.user?.email }}
                    </div>
                </div>
                <div :style="{ display:'flex', alignItems:'center', gap:'4px', flexShrink:0 }">
                    <div :style="{ width:'6px', height:'6px', borderRadius:'50%', background:'#16a34a' }"></div>
                    <span :style="{ fontFamily:'DM Mono, monospace', fontSize:'9px', color:'var(--text-3)', letterSpacing:'.08em' }">Online</span>
                </div>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    showMobile: { type: Boolean, default: false },
    isDark:     { type: Boolean, default: false }
})
defineEmits(['close'])

const isMobileScreen = ref(false)
const page = usePage()

const checkMobile = () => { isMobileScreen.value = window.innerWidth < 1024 }

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
})
onUnmounted(() => window.removeEventListener('resize', checkMobile))

const isActive = (name) => {
    const current = page.url || ''
    const routeMap = {
        dashboard: ['/dashboard'],
        users:     ['/users'],
        events:    ['/events'],
        bookings:  ['/bookings'],
        messages:  ['/messages'],
        venues:    ['/venues'],
        vendors:   ['/vendors'],
        profile:   ['/profile'],
        settings:  ['/settings'],
    }
    return (routeMap[name] || []).some(path => current.startsWith(path))
}
</script>