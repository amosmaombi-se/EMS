<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { reactive, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    events:     Object,
    eventTypes: Array,
    filters:    Object,
});

const form = reactive({
    search:        props.filters.search        || '',
    status:        props.filters.status        || '',
    event_type_id: props.filters.event_type_id || '',
    start_date:    props.filters.start_date    || '',
    end_date:      props.filters.end_date      || '',
});

const hasActiveFilters = computed(() =>
    form.search || form.status || form.event_type_id || form.start_date || form.end_date
);

const search = () => router.get(route('events.index'), form, { preserveState: true, preserveScroll: true });

const resetFilters = () => {
    Object.assign(form, { search: '', status: '', event_type_id: '', start_date: '', end_date: '' });
    search();
};

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
    : 'TBA';

const formatDay = (d) => d ? new Date(d).getDate() : '–';
const formatMon = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short' }) : '';

const STATUS = {
    draft:     { label: 'Draft',     color: '#9E9890', dot: '#9E9890'  },
    planning:  { label: 'Planning',  color: '#1D5C96', dot: '#1D5C96'  },
    confirmed: { label: 'Confirmed', color: '#16a34a', dot: '#16a34a'  },
    ongoing:   { label: 'Live Now',  color: '#F05A00', dot: '#F9B233'  },
    completed: { label: 'Wrapped',   color: '#C0170F', dot: '#C0170F'  },
    cancelled: { label: 'Cancelled', color: '#6B6560', dot: '#9E9890'  },
};

const getStatus     = (s) => STATUS[s] || STATUS.draft;
const countByStatus = (s) => (props.events?.data || []).filter(e => e.status === s).length;

const CARD_ACCENTS = [
    'linear-gradient(135deg, #C0170F 0%, #F05A00 50%, #F9B233 100%)',
    'linear-gradient(135deg, #1D5C96 0%, #C0170F 100%)',
    'linear-gradient(135deg, #F9B233 0%, #F05A00 100%)',
    'linear-gradient(135deg, #C0170F 0%, #8B0000 100%)',
    'linear-gradient(135deg, #1D5C96 0%, #0d3a6e 100%)',
    'linear-gradient(135deg, #F05A00 0%, #C0170F 100%)',
];
const cardAccent = (i) => CARD_ACCENTS[i % CARD_ACCENTS.length];
const guestColors = ['#C0170F', '#1D5C96', '#F05A00'];
</script>

<template>
    <AuthenticatedLayout>

        <component :is="'style'">
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
            .ep-ev *, .ep-ev *::before, .ep-ev *::after { box-sizing: border-box; }

            .ep-ev {
                --c: #C0170F; --o: #F05A00; --a: #F9B233; --n: #1D5C96;
                --bg: #F7F5F2; --card: #FFFFFF; --muted: #F0EDE8;
                --bdr: #E8E2DA; --t: #1A1410; --t2: #6B6560; --t3: #9E9890;
                --sh: 0 2px 20px rgba(0,0,0,.08);
                --sh2: 0 12px 40px rgba(0,0,0,.15);
                font-family: 'DM Sans', sans-serif;
                background: var(--bg); min-height: 100vh;
                padding: 28px 24px 64px; position: relative; overflow-x: hidden;
            }

            /* ── Floating confetti ── */
            .ep-confetti { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
            .ep-dot { position:absolute; opacity:0; animation:ep-rise linear infinite; }
            @keyframes ep-rise {
                0%   { transform:translateY(110vh) rotate(0deg);   opacity:0; }
                5%   { opacity:.5; }
                95%  { opacity:.3; }
                100% { transform:translateY(-80px) rotate(540deg); opacity:0; }
            }

            .ep-body { position:relative; z-index:1; }

            /* ── Header ── */
            .ep-hdr { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; margin-bottom:32px; flex-wrap:wrap; }
            .ep-eyebrow { display:inline-flex; align-items:center; gap:7px; font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.22em; color:var(--c); text-transform:uppercase; margin-bottom:6px; }
            .ep-live { width:7px; height:7px; border-radius:50%; background:var(--c); animation:ep-blink 1.2s ease-in-out infinite; }
            @keyframes ep-blink { 0%,100%{opacity:1;} 50%{opacity:.15;} }

            .ep-title { font-family:'Playfair Display',serif; font-size:clamp(30px,4vw,48px); font-weight:900; color:var(--t); margin:0 0 5px; line-height:1.05; letter-spacing:-.5px; }
            .ep-title em { font-style:italic; color:var(--c); }
            .ep-sub { font-size:14px; color:var(--t2); }

            .ep-cta {
                display:inline-flex; align-items:center; gap:9px;
                padding:13px 22px; border:none; border-radius:14px;
                background-size:200% auto;
                background-image:linear-gradient(135deg,var(--c) 0%,var(--o) 50%,var(--a) 100%);
                color:#fff; font-size:13px; font-weight:700;
                font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none;
                box-shadow:0 6px 24px rgba(192,23,15,.38);
                animation:ep-shine 3s linear infinite;
                transition:transform .2s,box-shadow .2s; white-space:nowrap;
            }
            @keyframes ep-shine { 0%{background-position:0% center;} 100%{background-position:200% center;} }
            .ep-cta:hover { transform:translateY(-2px) scale(1.02); box-shadow:0 10px 32px rgba(192,23,15,.5); }
            .ep-cta-icon { width:22px; height:22px; border-radius:6px; background:rgba(255,255,255,.25); display:flex; align-items:center; justify-content:center; }

            /* ── Stats ── */
            .ep-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
            @media(max-width:900px){.ep-stats{grid-template-columns:repeat(2,1fr);}}
            @media(max-width:500px){.ep-stats{grid-template-columns:1fr;}}

            .ep-stat {
                border-radius:20px; padding:20px; position:relative; overflow:hidden;
                box-shadow:var(--sh); transition:transform .22s,box-shadow .22s;
            }
            .ep-stat:hover { transform:translateY(-4px) scale(1.02); box-shadow:var(--sh2); }
            .ep-stat::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 75% 20%,rgba(255,255,255,.18) 0%,transparent 55%); pointer-events:none; }
            .ep-stat-emoji { font-size:30px; display:block; margin-bottom:10px; filter:drop-shadow(0 2px 6px rgba(0,0,0,.25)); }
            .ep-stat-n { font-family:'Playfair Display',serif; font-size:46px; font-weight:900; color:#fff; line-height:1; text-shadow:0 2px 10px rgba(0,0,0,.2); }
            .ep-stat-l { font-family:'DM Mono',monospace; font-size:9px; letter-spacing:.2em; color:rgba(255,255,255,.65); text-transform:uppercase; margin-top:8px; }

            /* ── Filters ── */
            .ep-fbox { background:var(--card); border:1px solid var(--bdr); border-radius:20px; padding:18px 22px; margin-bottom:22px; box-shadow:var(--sh); }
            .ep-ftop { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
            .ep-fh { font-family:'Playfair Display',serif; font-size:16px; font-weight:700; color:var(--t); }
            .ep-fs { font-family:'DM Mono',monospace; font-size:10px; color:var(--t3); margin-top:2px; }
            .ep-clr { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; cursor:pointer; border:1.5px solid rgba(192,23,15,.25); background:rgba(192,23,15,.07); color:var(--c); font-size:11px; font-weight:700; font-family:'DM Sans',sans-serif; transition:all .18s; }
            .ep-clr:hover { background:rgba(192,23,15,.14); }
            .ep-fgrid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr; gap:10px; }
            @media(max-width:1024px){.ep-fgrid{grid-template-columns:1fr 1fr 1fr;}}
            @media(max-width:640px){.ep-fgrid{grid-template-columns:1fr;}}
            .ep-fw { position:relative; }
            .ep-fic { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--t3); pointer-events:none; }
            .ep-in,.ep-sel { width:100%; padding:10px 12px 10px 34px; border:1.5px solid var(--bdr); border-radius:11px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--t); background:var(--bg); outline:none; transition:border-color .18s,box-shadow .18s; }
            .ep-sel { padding-left:12px; }
            .ep-in:focus,.ep-sel:focus { border-color:var(--c); box-shadow:0 0 0 3px rgba(192,23,15,.1); background:#fff; }
            .ep-in::placeholder { color:var(--t3); font-size:12px; }

            /* ── Results bar ── */
            .ep-rbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
            .ep-rtext { font-size:13px; color:var(--t2); }
            .ep-rtext b { color:var(--t); }
            .ep-rsort { padding:7px 12px; border:1.5px solid var(--bdr); border-radius:9px; font-size:12px; font-family:'DM Mono',monospace; color:var(--t2); background:var(--card); outline:none; }

            /* ── Grid ── */
            .ep-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
            @media(max-width:1100px){.ep-grid{grid-template-columns:repeat(2,1fr);}}
            @media(max-width:640px){.ep-grid{grid-template-columns:1fr;}}

            /* ── Card ── */
            .ep-card {
                background:var(--card); border:1px solid var(--bdr);
                border-radius:22px; overflow:hidden;
                box-shadow:var(--sh); display:flex; flex-direction:column;
                transition:transform .28s cubic-bezier(.34,1.56,.64,1),box-shadow .28s;
                position:relative;
            }
            .ep-card:hover { transform:translateY(-7px) rotate(-.25deg); box-shadow:var(--sh2); }
            .ep-card:hover .ep-ctitle { color:var(--c); }

            /* Banner */
            .ep-banner { height:88px; position:relative; overflow:hidden; flex-shrink:0; }
            .ep-bgrad { position:absolute; inset:0; }
            .ep-bnoise { position:absolute; inset:0; opacity:.09; background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }

            /* Sparkles in banner */
            .ep-sp { position:absolute; border-radius:50%; background:rgba(255,255,255,.8); animation:ep-twinkle ease-in-out infinite; }
            @keyframes ep-twinkle { 0%,100%{opacity:0;transform:scale(0);} 50%{opacity:1;transform:scale(1);} }

            /* Date pill */
            .ep-datepill {
                position:absolute; bottom:-20px; left:18px;
                background:var(--card); border:2.5px solid var(--bdr);
                border-radius:13px; padding:7px 13px;
                box-shadow:0 4px 16px rgba(0,0,0,.13);
                display:flex; flex-direction:column; align-items:center; min-width:50px;
                z-index:2;
            }
            .ep-dday { font-family:'Playfair Display',serif; font-size:24px; font-weight:900; color:var(--t); line-height:1; }
            .ep-dmon { font-family:'DM Mono',monospace; font-size:9px; letter-spacing:.14em; color:var(--c); text-transform:uppercase; margin-top:1px; }

            /* Status pill */
            .ep-spill {
                position:absolute; top:10px; right:12px;
                display:inline-flex; align-items:center; gap:5px;
                padding:4px 11px; border-radius:20px;
                background:rgba(0,0,0,.38); backdrop-filter:blur(8px);
                font-family:'DM Mono',monospace; font-size:9px;
                font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#fff;
            }
            .ep-sdot { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }
            .ep-spill.live .ep-sdot { animation:ep-blink .75s ease-in-out infinite; }

            /* Body */
            .ep-cbody { padding:30px 18px 14px; flex:1; display:flex; flex-direction:column; }
            .ep-ctype { font-family:'DM Mono',monospace; font-size:10px; letter-spacing:.14em; color:var(--t3); text-transform:uppercase; margin-bottom:7px; }
            .ep-ctitle { font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--t); line-height:1.3; margin-bottom:14px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; transition:color .2s; flex:1; }

            .ep-meta { display:flex; flex-direction:column; gap:7px; margin-bottom:14px; }
            .ep-mrow { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--t2); }
            .ep-mic { color:var(--t3); flex-shrink:0; }

            /* Guests */
            .ep-guests { display:flex; align-items:center; gap:6px; margin-bottom:16px; }
            .ep-gav { width:24px; height:24px; border-radius:50%; border:2.5px solid var(--card); display:flex; align-items:center; justify-content:center; font-size:10px; color:#fff; font-weight:700; margin-right:-8px; flex-shrink:0; }
            .ep-glbl { font-size:12px; color:var(--t2); font-family:'DM Mono',monospace; margin-left:18px; }

            /* Footer */
            .ep-cfoot { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-top:1px solid var(--bdr); background:var(--muted); gap:8px; }
            .ep-ibtn { width:32px; height:32px; border-radius:9px; border:1.5px solid var(--bdr); background:var(--card); color:var(--t3); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; transition:all .18s; }
            .ep-ibtn:hover { color:var(--n); background:rgba(29,92,150,.08); border-color:rgba(29,92,150,.25); }
            .ep-vbtn { display:inline-flex; align-items:center; gap:6px; padding:8px 17px; border-radius:10px; text-decoration:none; background:var(--t); color:#fff; font-size:12px; font-weight:700; font-family:'DM Sans',sans-serif; transition:all .2s; position:relative; overflow:hidden; }
            .ep-vbtn::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,var(--c),var(--o)); opacity:0; transition:opacity .2s; }
            .ep-vbtn:hover::before { opacity:1; }
            .ep-vbtn:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(192,23,15,.3); }
            .ep-vbtn > * { position:relative; z-index:1; }

            /* ── Empty ── */
            .ep-empty { background:var(--card); border:1px solid var(--bdr); border-radius:24px; padding:72px 24px; text-align:center; box-shadow:var(--sh); position:relative; overflow:hidden; }
            .ep-empty::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse at 50% 0%,rgba(192,23,15,.05) 0%,transparent 65%); pointer-events:none; }
            .ep-ej { font-size:60px; display:block; margin-bottom:16px; animation:ep-bounce 2s ease-in-out infinite; position:relative; }
            @keyframes ep-bounce { 0%,100%{transform:translateY(0) rotate(-3deg);} 50%{transform:translateY(-10px) rotate(3deg);} }
            .ep-et { font-family:'Playfair Display',serif; font-size:26px; font-weight:900; color:var(--t); margin-bottom:8px; position:relative; }
            .ep-es { font-size:14px; color:var(--t2); margin-bottom:28px; max-width:340px; margin-left:auto; margin-right:auto; position:relative; }
            .ep-ea { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; position:relative; }
            .ep-gbtn { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:12px; border:1.5px solid var(--bdr); background:var(--card); color:var(--t2); font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none; transition:all .18s; }
            .ep-gbtn:hover { border-color:var(--t3); color:var(--t); }
        </component>

        <!-- Confetti -->
        <div class="ep-confetti" aria-hidden="true">
            <div class="ep-dot" v-for="n in 20" :key="n" :style="{
                width:  (3 + (n*3)%9) + 'px',
                height: (3 + (n*3)%9) + 'px',
                left:   (n * 4.9 % 100) + '%',
                background: ['#C0170F','#F05A00','#F9B233','#1D5C96','#C0170F','#F9B233'][n%6],
                animationDuration: (9 + n*1.1) + 's',
                animationDelay:    (n * 0.6) + 's',
                borderRadius: n%3===0 ? '2px' : '50%',
            }"></div>
        </div>

        <div class="ep-ev">
        <div class="ep-body">

            <!-- Header -->
            <div class="ep-hdr">
                <div>
                    <div class="ep-eyebrow">
                        <span class="ep-live"></span>
                        Event Management
                    </div>
                    <h1 class="ep-title">Your <em>Events</em></h1>
                    <p class="ep-sub">Manage, track and celebrate every occasion</p>
                </div>
                <Link :href="route('events.create')" class="ep-cta">
                    <span class="ep-cta-icon">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                    </span>
                    Create Event
                </Link>
            </div>

            <!-- Stats -->
            <div v-if="events.data.length > 0" class="ep-stats">
                <div class="ep-stat" :style="{ background:'linear-gradient(135deg,#C0170F,#8B0000)' }">
                    <span class="ep-stat-emoji">🎪</span>
                    <div class="ep-stat-n">{{ events.total }}</div>
                    <div class="ep-stat-l">Total Events</div>
                </div>
                <div class="ep-stat" :style="{ background:'linear-gradient(135deg,#16a34a,#14532d)' }">
                    <span class="ep-stat-emoji">🎟️</span>
                    <div class="ep-stat-n">{{ countByStatus('confirmed') }}</div>
                    <div class="ep-stat-l">Confirmed</div>
                </div>
                <div class="ep-stat" :style="{ background:'linear-gradient(135deg,#F9B233,#F05A00)' }">
                    <span class="ep-stat-emoji">🎉</span>
                    <div class="ep-stat-n">{{ countByStatus('ongoing') }}</div>
                    <div class="ep-stat-l">Live Now</div>
                </div>
                <div class="ep-stat" :style="{ background:'linear-gradient(135deg,#1D5C96,#0d3a6e)' }">
                    <span class="ep-stat-emoji">🏆</span>
                    <div class="ep-stat-n">{{ countByStatus('completed') }}</div>
                    <div class="ep-stat-l">Wrapped</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="ep-fbox">
                <div class="ep-ftop">
                    <div>
                        <div class="ep-fh">Find Events</div>
                        <div class="ep-fs">Search · Filter · Discover</div>
                    </div>
                    <button v-if="hasActiveFilters" @click="resetFilters" class="ep-clr">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Clear All
                    </button>
                </div>
                <div class="ep-fgrid">
                    <div class="ep-fw">
                        <svg class="ep-fic" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input v-model="form.search" @input="search" type="text" placeholder="Search events…" class="ep-in" />
                    </div>
                    <select v-model="form.status" @change="search" class="ep-sel">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="planning">Planning</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select v-model="form.event_type_id" @change="search" class="ep-sel">
                        <option value="">All Types</option>
                        <option v-for="type in eventTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                    <input v-model="form.start_date" @change="search" type="date" class="ep-in" style="padding-left:12px" />
                    <input v-model="form.end_date"   @change="search" type="date" class="ep-in" style="padding-left:12px" />
                </div>
            </div>

            <!-- Results bar -->
            <div v-if="events.data.length > 0" class="ep-rbar">
                <p class="ep-rtext">Showing <b>{{ events.from }}</b>–<b>{{ events.to }}</b> of <b>{{ events.total }}</b> events</p>
                <select class="ep-rsort">
                    <option>Date (Newest)</option>
                    <option>Date (Oldest)</option>
                    <option>Name (A–Z)</option>
                    <option>Name (Z–A)</option>
                </select>
            </div>

            <!-- Grid -->
            <div v-if="events.data.length > 0" class="ep-grid">
                <div v-for="(event, i) in events.data" :key="event.id" class="ep-card">

                    <!-- Banner -->
                    <div class="ep-banner">
                        <div class="ep-bgrad" :style="{ background: cardAccent(i) }"></div>
                        <div class="ep-bnoise"></div>
                        <!-- Sparkles -->
                        <div class="ep-sp" v-for="s in 7" :key="s" :style="{
                            left: (s*14+2)+'%', top: (s%3*28+10)+'%',
                            width: s%2===0?'4px':'3px', height: s%2===0?'4px':'3px',
                            animationDuration: (1.4+s*.35)+'s',
                            animationDelay: (s*.2)+'s',
                        }"></div>

                        <!-- Date pill -->
                        <div class="ep-datepill">
                            <span class="ep-dday">{{ formatDay(event.event_date) }}</span>
                            <span class="ep-dmon">{{ formatMon(event.event_date) }}</span>
                        </div>

                        <!-- Status pill -->
                        <div :class="['ep-spill', event.status === 'ongoing' ? 'live' : '']"
                             :style="{ color: getStatus(event.status).dot }">
                            <span class="ep-sdot"></span>
                            {{ getStatus(event.status).label }}
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="ep-cbody">
                        <div class="ep-ctype">{{ event.event_type?.name || 'Event' }}</div>
                        <div class="ep-ctitle">{{ event.title }}</div>

                        <div class="ep-meta">
                            <div class="ep-mrow">
                                <svg class="ep-mic" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>{{ formatDate(event.event_date) }}</span>
                            </div>
                            <div v-if="event.city" class="ep-mrow">
                                <svg class="ep-mic" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>{{ event.city }}, {{ event.country }}</span>
                            </div>
                        </div>

                        <!-- Guests -->
                        <div class="ep-guests">
                            <div
                                v-for="(g, gi) in Math.min(3, Math.max(1, Math.ceil((event.expected_guests||1)/100)))"
                                :key="gi" class="ep-gav"
                                :style="{ background: guestColors[gi], zIndex: 4-gi }">
                                {{ ['🎊','🎈','✨'][gi] }}
                            </div>
                            <span class="ep-glbl">{{ (event.expected_guests||0).toLocaleString() }} guests</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="ep-cfoot">
                        <Link :href="route('events.edit', event.id)" class="ep-ibtn" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                        </Link>
                        <Link :href="route('events.show', event.id)" class="ep-vbtn">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="events.data.length === 0" class="ep-empty">
                <span class="ep-ej">🎪</span>
                <div class="ep-et">No events yet!</div>
                <p class="ep-es">{{ hasActiveFilters ? "Try adjusting your filters — the party's out there somewhere." : 'Time to plan something amazing. Create your first event!' }}</p>
                <div class="ep-ea">
                    <Link v-if="!hasActiveFilters" :href="route('events.create')" class="ep-cta" style="animation:none">
                        <span class="ep-cta-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></span>
                        Create First Event
                    </Link>
                    <button v-else @click="resetFilters" class="ep-gbtn">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="events.data.length > 0" style="margin-top:28px">
                <Pagination :links="events.links" />
            </div>

        </div>
        </div>
    </AuthenticatedLayout>
</template>