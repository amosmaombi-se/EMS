<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <component :is="'style'">
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

            :root {
                --crimson: #C0170F;
                --orange:  #F05A00;
                --amber:   #F9B233;
                --navy:    #1D5C96;
            }

            .ep-dash * { box-sizing: border-box; }

            .ep-dash {
                --bg:           #F7F5F2;
                --bg-card:      #FFFFFF;
                --bg-muted:     #F0EDE8;
                --border:       #E8E2DA;
                --text:         #1A1410;
                --text-2:       #6B6560;
                --text-3:       #9E9890;
                --font-body:    'DM Sans', sans-serif;
                --font-display: 'Playfair Display', serif;
                --font-mono:    'DM Mono', monospace;
                --shadow:       0 2px 16px rgba(0,0,0,.08);
                --shadow-lg:    0 8px 40px rgba(0,0,0,.13);
                --accent-bar:   linear-gradient(90deg, var(--crimson), var(--orange), var(--amber), var(--navy));
            }

            .ep-dash.dark {
                --bg:        #0F0D0C;
                --bg-card:   #1A1714;
                --bg-muted:  #242018;
                --border:    #2E2925;
                --text:      #F0EDE8;
                --text-2:    #A09890;
                --text-3:    #605850;
                --shadow:    0 2px 16px rgba(0,0,0,.4);
                --shadow-lg: 0 8px 40px rgba(0,0,0,.6);
            }

            .ep-dash {
                min-height: 100vh;
                background: var(--bg);
                color: var(--text);
                font-family: var(--font-body);
                transition: background .3s, color .3s;
            }

            .ep-accent-bar { height: 3px; background: var(--accent-bar); }

            .ep-card {
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 20px;
                box-shadow: var(--shadow);
                transition: transform .22s, box-shadow .22s;
            }
            .ep-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

            .ep-stat {
                border-radius: 20px;
                padding: 22px 20px;
                position: relative;
                overflow: hidden;
                cursor: pointer;
                box-shadow: var(--shadow);
                transition: transform .22s, box-shadow .22s;
            }
            .ep-stat:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
            .ep-stat-ghost {
                position: absolute; top: -16px; right: -16px;
                font-size: 76px; opacity: .12; pointer-events: none; line-height: 1;
            }

            .ep-table { width: 100%; border-collapse: collapse; }
            .ep-table thead th {
                padding: 10px 20px; text-align: left;
                font-size: 10px; font-family: var(--font-mono);
                letter-spacing: .15em; color: var(--text-3); font-weight: 600;
                background: var(--bg-muted); border-bottom: 1px solid var(--border);
            }
            .ep-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
            .ep-table tbody tr:hover { background: var(--bg-muted); }
            .ep-table tbody td { padding: 13px 20px; font-size: 13px; }

            .ep-badge {
                display: inline-block; padding: 3px 10px; border-radius: 6px;
                font-size: 10px; font-family: var(--font-mono);
                font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
            }

            .ep-progress { height: 4px; background: var(--border); border-radius: 2px; overflow: hidden; }
            .ep-progress-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

            .ep-btn-period {
                padding: 5px 13px; border-radius: 8px; border: none; cursor: pointer;
                font-size: 11px; font-family: var(--font-mono); letter-spacing: .08em; font-weight: 600;
                transition: all .2s;
            }
            .ep-btn-period.active { background: var(--crimson); color: #fff; }
            .ep-btn-period:not(.active) { background: var(--bg-muted); color: var(--text-2); }

            /* Info pills */
            .ep-info-pill {
                display: inline-flex; align-items: center; gap: 7px;
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 8px 14px;
                box-shadow: var(--shadow);
                font-size: 12px;
                font-family: var(--font-mono);
                color: var(--text-2);
                white-space: nowrap;
            }

            .ep-label {
                font-size: 10px; font-family: var(--font-mono);
                letter-spacing: .2em; color: var(--text-3); text-transform: uppercase; margin-bottom: 14px;
            }

            @media (max-width: 1024px) {
                .ep-grid-main { grid-template-columns: 1fr !important; }
            }
            @media (max-width: 768px) {
                .ep-grid-stats { grid-template-columns: 1fr 1fr !important; }
                .ep-grid-3 { grid-template-columns: 1fr !important; }
                .ep-header-row { flex-direction: column !important; gap: 16px !important; align-items: flex-start !important; }
            }
            @media (max-width: 480px) {
                .ep-grid-stats { grid-template-columns: 1fr !important; }
            }

            .ep-upcoming-card {
                background: var(--bg-muted);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 16px;
                transition: all .2s;
                cursor: pointer;
            }
            .ep-upcoming-card:hover { border-color: var(--orange); box-shadow: var(--shadow); }

            .ep-rsvp-box {
                border-radius: 12px;
                padding: 12px 14px;
                border-left: 3px solid;
                background: var(--bg-muted);
            }

            .ep-chart-wrap {
                position: relative; height: 160px;
                background: var(--bg-muted); border-radius: 14px; padding: 12px;
            }

            .ep-headline { font-family: var(--font-display); }
        </component>

        <div :class="['ep-dash', isDark ? 'dark' : '']">
            <div class="ep-accent-bar"></div>

            <!-- Loading overlay -->
            <div v-if="isLoading"
                :style="{ position:'fixed', inset:0, background: isDark ? 'rgba(15,13,12,.85)' : 'rgba(247,245,242,.85)', backdropFilter:'blur(8px)', zIndex:50, display:'flex', alignItems:'center', justifyContent:'center' }">
                <div style="text-align:center">
                    <div style="position:relative; width:56px; height:56px; margin:0 auto 14px">
                        <div :style="{ position:'absolute', inset:0, border:'3px solid transparent', borderTopColor:'var(--crimson)', borderRadius:'50%', animation:'spin 0.8s linear infinite' }"></div>
                        <div :style="{ position:'absolute', inset:'6px', border:'3px solid transparent', borderBottomColor:'var(--amber)', borderRadius:'50%', animation:'spin 1.2s linear infinite reverse' }"></div>
                    </div>
                    <p :style="{ color:'var(--text-2)', fontFamily:'var(--font-mono)', fontSize:'12px', letterSpacing:'.1em' }">LOADING DASHBOARD...</p>
                </div>
                <style>@keyframes spin { to { transform:rotate(360deg) } }</style>
            </div>

            <div v-else :style="{ padding:'0 24px 48px', maxWidth:'1400px', margin:'0 auto' }">

                <!-- ── HEADER ── -->
                <div class="ep-header-row" :style="{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', padding:'28px 0 28px', borderBottom:'1px solid var(--border)', marginBottom:'28px', gap:'20px' }">
                    <div>
                        <div :style="{ display:'flex', alignItems:'center', gap:'10px', marginBottom:'10px' }">
                            <div :style="{ width:'32px', height:'32px', borderRadius:'8px', background:'var(--crimson)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'16px' }">🚪</div>
                            <span :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', letterSpacing:'.25em', color:'var(--text-3)', textTransform:'uppercase' }">Event Portal</span>
                        </div>
                        <h1 class="ep-headline" :style="{ fontSize:'clamp(26px, 4vw, 40px)', fontWeight:900, margin:0, lineHeight:1.1, color:'var(--text)' }">
                            {{ greeting }}, <span :style="{ color:'var(--crimson)' }">Admin</span> 👋
                        </h1>
                        <p :style="{ margin:'6px 0 0', color:'var(--text-2)', fontSize:'13px', fontFamily:'var(--font-body)' }">Here's what's happening with your events today.</p>
                    </div>

                    <!-- Right: date pill + location pill only -->
                    <div :style="{ display:'flex', flexDirection:'column', alignItems:'flex-end', gap:'10px', paddingTop:'6px' }">
                        <!-- Date pill -->
                        <div class="ep-info-pill">
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;opacity:.7">
                                <rect x="1" y="2" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M5 1v2M11 1v2M1 6h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span>{{ currentDate }}</span>
                        </div>
                        <!-- Location pill -->
                        <div class="ep-info-pill">
                            <svg width="11" height="14" viewBox="0 0 11 14" fill="none" style="flex-shrink:0;opacity:.7">
                                <path d="M5.5 0C3.015 0 1 2.015 1 4.5 1 7.875 5.5 13 5.5 13S10 7.875 10 4.5C10 2.015 7.985 0 5.5 0z" stroke="currentColor" stroke-width="1.4"/>
                                <circle cx="5.5" cy="4.5" r="1.5" stroke="currentColor" stroke-width="1.4"/>
                            </svg>
                            <span>Dar es Salaam, TZ</span>
                        </div>
                    </div>
                </div>

                <!-- ── STAT CARDS ── -->
                <div class="ep-grid-stats" :style="{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:'16px', marginBottom:'28px' }">
                    <div class="ep-stat" :style="{ background:'linear-gradient(135deg, var(--crimson), #8B0000)' }">
                        <span class="ep-stat-ghost">📅</span>
                        <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', letterSpacing:'.2em', color:'rgba(255,255,255,.7)', marginBottom:'10px' }">TOTAL EVENTS</div>
                        <div :style="{ fontSize:'42px', fontWeight:900, color:'#fff', lineHeight:1, marginBottom:'6px', fontFamily:'var(--font-display)' }">{{ stats.total_events }}</div>
                        <div :style="{ fontSize:'11px', color:'rgba(255,255,255,.75)', fontFamily:'var(--font-mono)' }">↑ +{{ stats.events_this_month }} this month</div>
                    </div>
                    <div class="ep-stat" :style="{ background:'linear-gradient(135deg, var(--orange), var(--crimson))' }">
                        <span class="ep-stat-ghost">👥</span>
                        <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', letterSpacing:'.2em', color:'rgba(255,255,255,.7)', marginBottom:'10px' }">TOTAL GUESTS</div>
                        <div :style="{ fontSize:'42px', fontWeight:900, color:'#fff', lineHeight:1, marginBottom:'6px', fontFamily:'var(--font-display)' }">{{ formatNumber(stats.total_guests) }}</div>
                        <div :style="{ fontSize:'11px', color:'rgba(255,255,255,.75)', fontFamily:'var(--font-mono)' }">✓ {{ formatNumber(stats.total_attending) }} attending</div>
                    </div>
                    <div class="ep-stat" :style="{ background:'linear-gradient(135deg, var(--amber), var(--orange))' }">
                        <span class="ep-stat-ghost">💰</span>
                        <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', letterSpacing:'.2em', color:'rgba(255,255,255,.7)', marginBottom:'10px' }">REVENUE</div>
                        <div :style="{ fontSize:'42px', fontWeight:900, color:'#fff', lineHeight:1, marginBottom:'6px', fontFamily:'var(--font-display)' }">TZS{{ formatShort(stats.total_revenue) }}</div>
                        <div :style="{ fontSize:'11px', color:'rgba(255,255,255,.75)', fontFamily:'var(--font-mono)' }">⏳ {{ stats.pending_bookings }} pending</div>
                    </div>
                    <div class="ep-stat" :style="{ background:'linear-gradient(135deg, var(--navy), #0d3a6e)' }">
                        <span class="ep-stat-ghost">⏰</span>
                        <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', letterSpacing:'.2em', color:'rgba(255,255,255,.7)', marginBottom:'10px' }">UPCOMING</div>
                        <div :style="{ fontSize:'42px', fontWeight:900, color:'#fff', lineHeight:1, marginBottom:'6px', fontFamily:'var(--font-display)' }">{{ stats.upcoming_events }}</div>
                        <div :style="{ fontSize:'11px', color:'rgba(255,255,255,.75)', fontFamily:'var(--font-mono)' }">🏪 {{ stats.active_vendors }} vendors</div>
                    </div>
                </div>

                <!-- ── FEATURE CARDS ── -->
                <div class="ep-grid-3" :style="{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:'16px', marginBottom:'28px' }">
                    <div v-for="(event, index) in recentEvents.slice(0, 3)" :key="event.id" class="ep-card" :style="{ padding:'20px', borderTop: `3px solid ${featureColors[index]}` }">
                        <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', marginBottom:'14px' }">
                            <div :style="{ width:'40px', height:'40px', borderRadius:'12px', background:featureColors[index], display:'flex', alignItems:'center', justifyContent:'center', fontSize:'18px', flexShrink:0 }">
                                {{ getEventEmoji(event.event_type) }}
                            </div>
                            <span :style="{ fontSize:'11px', color:'var(--text-3)', fontFamily:'var(--font-mono)' }">{{ event.date }}</span>
                        </div>
                        <h3 :style="{ fontSize:'15px', fontWeight:700, color:'var(--text)', margin:'0 0 10px', fontFamily:'var(--font-display)' }">{{ event.name }}</h3>
                        <div :style="{ display:'flex', alignItems:'center', marginBottom:'12px' }">
                            <div v-for="n in Math.min(4, event.confirmed_guests)" :key="n"
                                :style="{ width:'28px', height:'28px', borderRadius:'50%', border:'2px solid var(--bg-card)', background:featureColors[index], display:'flex', alignItems:'center', justifyContent:'center', fontSize:'10px', fontWeight:700, color:'#fff', marginLeft: n > 1 ? '-8px' : '0' }">
                                {{ String.fromCharCode(64 + n) }}
                            </div>
                            <div v-if="event.confirmed_guests > 4"
                                :style="{ width:'28px', height:'28px', borderRadius:'50%', border:'2px solid var(--bg-card)', background:'var(--bg-muted)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'10px', fontWeight:700, color:'var(--text-2)', marginLeft:'-8px' }">
                                +{{ event.confirmed_guests - 4 }}
                            </div>
                        </div>
                        <div :style="{ marginBottom:'12px' }">
                            <div :style="{ display:'flex', justifyContent:'space-between', fontSize:'11px', color:'var(--text-2)', marginBottom:'5px', fontFamily:'var(--font-mono)' }">
                                <span>COMPLETION</span>
                                <span :style="{ fontWeight:700, color:'var(--text)' }">{{ event.completion }}%</span>
                            </div>
                            <div class="ep-progress">
                                <div class="ep-progress-fill" :style="{ width: event.completion + '%', background: featureColors[index] }"></div>
                            </div>
                        </div>
                        <div :style="{ display:'flex', justifyContent:'space-between', paddingTop:'10px', borderTop:'1px solid var(--border)', fontSize:'12px', color:'var(--text-2)' }">
                            <span>👥 <strong :style="{ color:'var(--text)' }">{{ event.confirmed_guests }}/{{ event.guests_count }}</strong></span>
                            <span :style="{ fontFamily:'var(--font-mono)' }">{{ event.eventDay }}</span>
                        </div>
                    </div>
                </div>

                <!-- ── RECENT EVENTS TABLE ── -->
                <div class="ep-card" :style="{ overflow:'hidden', marginBottom:'28px' }">
                    <div :style="{ padding:'16px 22px', display:'flex', justifyContent:'space-between', alignItems:'center', borderBottom:'1px solid var(--border)', background:'var(--bg-muted)' }">
                        <div class="ep-label" :style="{ margin:0 }">📋 RECENT EVENTS</div>
                        <Link :href="route('events.index')" :style="{ fontSize:'11px', fontFamily:'var(--font-mono)', color:'var(--crimson)', background:'transparent', border:'1px solid var(--crimson)', borderRadius:'8px', padding:'4px 12px', textDecoration:'none', transition:'all .2s' }">
                            View All →
                        </Link>
                    </div>
                    <div :style="{ overflowX:'auto' }">
                        <table class="ep-table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Handler</th>
                                    <th>Event Day</th>
                                    <th>Guests</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="event in recentEvents" :key="event.id">
                                    <td>
                                        <div :style="{ display:'flex', alignItems:'center', gap:'12px' }">
                                            <div :style="{ width:'36px', height:'36px', borderRadius:'10px', background:'var(--bg-muted)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'16px', flexShrink:0 }">
                                                {{ getEventEmoji(event.event_type) }}
                                            </div>
                                            <div>
                                                <div :style="{ fontWeight:600, fontSize:'13px', color:'var(--text)', fontFamily:'var(--font-body)' }">{{ event.name }}</div>
                                                <div :style="{ fontSize:'11px', color:'var(--text-3)', fontFamily:'var(--font-mono)' }">{{ event.created }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td :style="{ color:'var(--text-2)' }">{{ event.handler }}</td>
                                    <td :style="{ color:'var(--text-2)', fontFamily:'var(--font-mono)', fontSize:'12px' }">{{ event.eventDay }}</td>
                                    <td :style="{ fontFamily:'var(--font-mono)', fontWeight:700, color:'var(--text)' }">
                                        {{ event.confirmed_guests }}<span :style="{ color:'var(--text-3)' }">/{{ event.guests_count }}</span>
                                    </td>
                                    <td :style="{ minWidth:'120px' }">
                                        <div :style="{ display:'flex', alignItems:'center', gap:'8px' }">
                                            <div class="ep-progress" :style="{ flex:1 }">
                                                <div class="ep-progress-fill" :style="{ width: event.completion + '%', background: event.completion > 75 ? 'var(--orange)' : 'var(--amber)' }"></div>
                                            </div>
                                            <span :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'var(--text-3)', minWidth:'28px' }">{{ event.completion }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ep-badge" :style="getStatusStyle(event.status_class)">{{ event.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ── BOTTOM GRID ── -->
                <div class="ep-grid-main" :style="{ display:'grid', gridTemplateColumns:'1fr 400px', gap:'20px' }">

                    <!-- Guest Overview + Chart -->
                    <div class="ep-card" :style="{ padding:'22px' }">
                        <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:'18px' }">
                            <div>
                                <div class="ep-label">👥 GUEST OVERVIEW</div>
                                <div :style="{ display:'flex', alignItems:'baseline', gap:'10px' }">
                                    <span class="ep-headline" :style="{ fontSize:'36px', fontWeight:900, color:'var(--text)' }">{{ formatNumber(stats.total_guests) }}</span>
                                    <span :style="{ fontSize:'12px', color:'#16a34a', background:'rgba(34,197,94,.1)', padding:'2px 8px', borderRadius:'20px', fontFamily:'var(--font-mono)' }">↑ +12%</span>
                                </div>
                            </div>
                            <div :style="{ display:'flex', gap:'6px' }">
                                <button class="ep-btn-period" :class="{ active: chartPeriod === 'monthly' }" @click="setChartPeriod('monthly')">Monthly</button>
                                <button class="ep-btn-period" :class="{ active: chartPeriod === 'weekly' }" @click="setChartPeriod('weekly')">Weekly</button>
                            </div>
                        </div>
                        <div :style="{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:'10px', marginBottom:'18px' }">
                            <div class="ep-rsvp-box" :style="{ borderLeftColor:'#16a34a' }">
                                <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'#16a34a', marginBottom:'4px' }">ATTENDING</div>
                                <div class="ep-headline" :style="{ fontSize:'22px', fontWeight:900, color:'var(--text)' }">{{ guestStats.rsvp_breakdown.attending }}</div>
                            </div>
                            <div class="ep-rsvp-box" :style="{ borderLeftColor:'var(--amber)' }">
                                <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'var(--orange)', marginBottom:'4px' }">PENDING</div>
                                <div class="ep-headline" :style="{ fontSize:'22px', fontWeight:900, color:'var(--text)' }">{{ guestStats.rsvp_breakdown.pending }}</div>
                            </div>
                            <div class="ep-rsvp-box" :style="{ borderLeftColor:'var(--crimson)' }">
                                <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'var(--crimson)', marginBottom:'4px' }">DECLINED</div>
                                <div class="ep-headline" :style="{ fontSize:'22px', fontWeight:900, color:'var(--text)' }">{{ guestStats.rsvp_breakdown.not_attending }}</div>
                            </div>
                            <div class="ep-rsvp-box" :style="{ borderLeftColor:'var(--navy)' }">
                                <div :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'var(--navy)', marginBottom:'4px' }">MAYBE</div>
                                <div class="ep-headline" :style="{ fontSize:'22px', fontWeight:900, color:'var(--text)' }">{{ guestStats.rsvp_breakdown.maybe }}</div>
                            </div>
                        </div>
                        <div class="ep-chart-wrap">
                            <canvas ref="participantChart"></canvas>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="ep-card" :style="{ padding:'22px' }">
                        <div class="ep-label">🗓 UPCOMING EVENTS</div>
                        <div :style="{ display:'flex', flexDirection:'column', gap:'10px', marginBottom:'16px' }">
                            <div v-for="(event, index) in upcomingEvents" :key="event.id" class="ep-upcoming-card"
                                :style="{ borderTop: `2px solid ${featureColors[index]}` }">
                                <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', marginBottom:'8px' }">
                                    <h3 :style="{ fontSize:'13px', fontWeight:700, color:'var(--text)', margin:0, fontFamily:'var(--font-display)', flex:1, paddingRight:'8px' }">{{ event.title }}</h3>
                                    <span class="ep-badge" :style="event.status === 'published' ? 'background:rgba(34,197,94,.12);color:#16a34a' : 'background:rgba(249,178,51,.15);color:#b45309'">
                                        {{ event.status }}
                                    </span>
                                </div>
                                <div :style="{ fontSize:'11px', color:'var(--text-2)', marginBottom:'2px', fontFamily:'var(--font-mono)' }">🕐 {{ event.date }} at {{ event.time }}</div>
                                <div :style="{ fontSize:'11px', color:'var(--text-3)', marginBottom:'10px' }">📍 {{ event.venue }}</div>
                                <div :style="{ display:'flex', justifyContent:'space-between', fontSize:'11px', marginBottom:'5px' }">
                                    <span :style="{ color:'var(--text-2)', fontFamily:'var(--font-mono)' }">CONFIRMATION</span>
                                    <span :style="{ fontWeight:700, fontFamily:'var(--font-mono)', color:'var(--text)' }">{{ event.confirmed_guests }}/{{ event.expected_guests }}</span>
                                </div>
                                <div class="ep-progress">
                                    <div class="ep-progress-fill" :style="{ width: (event.confirmed_guests / event.expected_guests * 100) + '%', background: featureColors[index] }"></div>
                                </div>
                            </div>
                        </div>
                        <Link :href="route('events.index')"
                            :style="{ display:'block', textAlign:'center', padding:'11px', background:'var(--crimson)', color:'#fff', borderRadius:'12px', fontSize:'13px', fontWeight:700, textDecoration:'none', transition:'opacity .2s', fontFamily:'var(--font-mono)', letterSpacing:'.05em' }">
                            View All Events →
                        </Link>
                    </div>
                </div>

                <!-- Footer -->
                <div :style="{ marginTop:'32px', display:'flex', justifyContent:'space-between', alignItems:'center', paddingTop:'18px', borderTop:'1px solid var(--border)' }">
                    <div :style="{ display:'flex', alignItems:'center', gap:'7px' }">
                        <div :style="{ width:'7px', height:'7px', borderRadius:'50%', background:'#16a34a', boxShadow:'0 0 6px #16a34a' }"></div>
                        <span :style="{ fontSize:'11px', fontFamily:'var(--font-mono)', color:'var(--text-3)' }">All systems operational</span>
                    </div>
                    <span :style="{ fontSize:'10px', fontFamily:'var(--font-mono)', color:'var(--text-3)', letterSpacing:'.08em' }">Event Portal © 2026 · Invite. Inform. Remind. Track.</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    stats: Object,
    recentEvents: Array,
    upcomingEvents: Array,
    recentBookings: Array,
    guestStats: Object
})

// ── State ──────────────────────────────────────────
const isLoading        = ref(true)
const isDark           = ref(false)
const participantChart = ref(null)
const chartPeriod      = ref('monthly')
let chartInstance      = null

// ── Brand colors ───────────────────────────────────
const featureColors = ['#C0170F', '#1D5C96', '#F05A00']

// ── Greeting ──────────────────────────────────────
const greeting = computed(() => {
    const h = new Date().getHours()
    return h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening'
})

// ── Static date pill (no ticking clock) ───────────
const currentDate = computed(() => {
    const now    = new Date()
    const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
    return `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()} ${now.getFullYear()}`
})

// ── Dark mode: synced from Header via CustomEvent ──
const syncDark = (e) => { isDark.value = e.detail }

// ── Lifecycle ──────────────────────────────────────
onMounted(() => {
    isDark.value = localStorage.getItem('ep-dark') === 'true'
    window.addEventListener('ep-dark-toggle', syncDark)

    setTimeout(() => {
        isLoading.value = false
        setTimeout(updateChart, 100)
    }, 1500)
})

onUnmounted(() => {
    window.removeEventListener('ep-dark-toggle', syncDark)
    if (chartInstance) chartInstance.destroy()
})

// ── Helpers ────────────────────────────────────────
const formatNumber = (n) => new Intl.NumberFormat().format(n || 0)
const formatShort  = (n) => {
    if (!n) return '0'
    if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
    if (n >= 1000)    return (n / 1000).toFixed(0) + 'K'
    return n.toString()
}

const getEventEmoji = (type) => {
    const map = { wedding:'💍', conference:'🎤', gala:'✨', corporate:'🏢', festival:'🎉' }
    return map[type] || '📅'
}

const getStatusStyle = (cls) => {
    const map = {
        active:    'background:rgba(192,23,15,.1);color:#C0170F',
        published: 'background:rgba(34,197,94,.1);color:#16a34a',
        pending:   'background:rgba(249,178,51,.15);color:#b45309',
        draft:     'background:rgba(29,92,150,.1);color:#1D5C96',
    }
    return map[cls] || map.draft
}

// ── Chart ──────────────────────────────────────────
const setChartPeriod = (period) => {
    chartPeriod.value = period
    updateChart()
}

const updateChart = () => {
    if (chartInstance) { chartInstance.destroy(); chartInstance = null }
    if (!participantChart.value) return

    const { labels, values } = chartPeriod.value === 'monthly' ? getMonthlyData() : getWeeklyData()
    const gridColor = isDark.value ? '#2E2925' : '#E8E2DA'
    const tickColor = isDark.value ? '#605850' : '#9E9890'

    chartInstance = new Chart(participantChart.value, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: '#F05A00',
                hoverBackgroundColor: '#C0170F',
                borderRadius: 5,
                barThickness: chartPeriod.value === 'monthly' ? 6 : 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw} guests` } } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { display: false } },
                x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 9, weight: '600', family: 'DM Mono' } } }
            }
        }
    })
}

const getMonthlyData = () => {
    const days = Array.from({ length: 31 }, (_, i) => (i + 1).toString())
    const vals = Object.values(props.guestStats?.daily || {}).length
        ? Object.values(props.guestStats.daily)
        : [150,200,180,220,190,250,240,210,280,260,290,310,280,320,350,380,360,340,370,350,330,310,290,270,250,230,210,190,170,150,130]
    return { labels: days, values: vals }
}

const getWeeklyData = () => ({
    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    values: [180,220,280,320,380,350,290]
})

watch(isDark, () => setTimeout(updateChart, 50))
watch(chartPeriod, updateChart)
</script>