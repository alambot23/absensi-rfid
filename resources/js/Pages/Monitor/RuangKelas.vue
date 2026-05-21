<template>
  <div class="monitor-wrapper" @click="refocusInput">

    <input
      ref="rfidInput"
      v-model="rfidBuffer"
      type="text"
      class="rfid-hidden-input"
      autocomplete="off"
      autocorrect="off"
      spellcheck="false"
      @keydown.enter.prevent="onEnterPressed"
      @blur="scheduledRefocus"
      tabindex="0"
      aria-label="Input RFID tersembunyi"
    />

    <header class="monitor-header">
      <div class="header-left">
        <div class="room-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9,22 9,12 15,12 15,22"/>
          </svg>
          {{ ruangan }}
        </div>
        <h1 class="header-title">SISTEM ABSENSI</h1>
        <p class="header-subtitle">Universitas — RFID Attendance</p>
      </div>
      <div class="header-right">
        <div class="clock-display">{{ currentTime }}</div>
        <div class="date-display">{{ currentDate }}</div>
        <div class="focus-indicator" :class="{ active: isInputFocused }">
          <span class="focus-dot"></span>
          {{ isInputFocused ? 'Reader Aktif' : 'Klik untuk Aktifkan' }}
        </div>
      </div>
    </header>

    <section class="jadwal-section">
      <div v-if="jadwalAktif" class="jadwal-card">
        <div class="jadwal-badge hadir-badge">KELAS BERLANGSUNG</div>
        <h2 class="matkul-name">{{ jadwalAktif.mata_kuliah }}</h2>
        <div class="jadwal-meta">
          <span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            {{ jadwalAktif.jam_mulai }} – {{ jadwalAktif.jam_selesai }}
          </span>
          <span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ jadwalAktif.dosen }}
          </span>
        </div>
      </div>
      <div v-else class="jadwal-kosong">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <p>Tidak ada kelas aktif saat ini</p>
        <span>Silakan tap kartu saat jadwal dimulai</span>
      </div>
    </section>

    <section class="tap-section">
      <div class="tap-animation" :class="{ processing: isProcessing }">
        <div class="tap-ring ring-1"></div>
        <div class="tap-ring ring-2"></div>
        <div class="tap-ring ring-3"></div>
        <div class="tap-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <rect x="2" y="5" width="20" height="14" rx="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </div>
      </div>
      <p class="tap-label" v-if="!isProcessing">Tap Kartu / ID Card</p>
      <p class="tap-label processing-label" v-else>Memproses...</p>
    </section>

    <section class="recent-section">
      <h3 class="recent-title">Kehadiran Terkini</h3>
      <div class="recent-list">
        <transition-group name="slide-in">
          <div
            v-for="item in recentAbsensi"
            :key="item.id"
            class="recent-item"
            :class="item.status"
          >
            <div class="recent-avatar">
              <img v-if="item.foto" :src="item.foto" :alt="item.nama" />
              <span v-else>{{ item.nama.charAt(0) }}</span>
            </div>
            <div class="recent-info">
              <span class="recent-nama">{{ item.nama }}</span>
              <span class="recent-nim">{{ item.nim }}</span>
            </div>
            <div class="recent-meta">
              <span class="recent-status-badge" :class="item.status">
                {{ item.status === 'hadir' ? 'Hadir' : item.status === 'terlambat' ? 'Terlambat' : item.status }}
              </span>
              <span class="recent-time">{{ item.waktu }}</span>
            </div>
          </div>
        </transition-group>
        <div v-if="recentAbsensi.length === 0" class="recent-empty">
          Belum ada absensi hari ini
        </div>
      </div>
    </section>

    <transition name="popup-fade">
      <div v-if="showPopup" class="popup-overlay" aria-live="polite" aria-atomic="true">
        <div class="popup-card" :class="popupData.type">
          <template v-if="popupData.type === 'berhasil'">
            <div class="popup-icon success-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <polyline points="20,6 9,17 4,12"/>
              </svg>
            </div>
            <div class="popup-photo-wrap">
              <img v-if="popupData.foto" :src="popupData.foto" :alt="popupData.nama" class="popup-photo" />
              <div v-else class="popup-photo-placeholder">{{ popupData.nama?.charAt(0) }}</div>
            </div>
            <div class="popup-status-badge" :class="popupData.status">
              {{ popupData.status === 'hadir' ? '✓ Hadir' : '⚠ Terlambat' }}
            </div>
            <h2 class="popup-nama">{{ popupData.nama }}</h2>
            <p class="popup-nim">{{ popupData.nim }}</p>
            <p class="popup-matkul">{{ popupData.matkul }}</p>
            <p class="popup-time">{{ popupData.waktu }}</p>
          </template>

          <template v-else>
            <div class="popup-icon error-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
            </div>
            <h2 class="popup-error-title">{{ popupData.errorTitle }}</h2>
            <p class="popup-error-msg">{{ popupData.message }}</p>
            <p v-if="popupData.nama" class="popup-nim">{{ popupData.nama }} — {{ popupData.nim }}</p>
          </template>

          <div class="popup-progress">
            <div class="popup-progress-bar" :style="{ animationDuration: popupDuration + 'ms' }"></div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
// PENTING: Gunakan axios agar CSRF token ditangani secara otomatis oleh ekosistem Laravel
import axios from 'axios';

export default {
  name: 'MonitorRuangKelas',

  props: {
    ruangan: {
      type: String,
      required: true,
    },
    ruanganSlug: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      // ── Buffer Input RFID ──────────────────────────────────
      rfidBuffer: '',
      isInputFocused: false,
      isProcessing: false,

      // ── Waktu Real-time ────────────────────────────────────
      currentTime: '',
      currentDate: '',
      clockTimer: null,

      // ── Jadwal Kuliah ──────────────────────────────────────
      jadwalAktif: null,
      jadwalPollTimer: null,

      // ── Popup Notifikasi ───────────────────────────────────
      showPopup: false,
      popupData: {},
      popupDuration: 5000,   // ms popup ditampilkan
      popupTimer: null,

      // ── Riwayat Absensi Terkini ────────────────────────────
      recentAbsensi: [],
      recentCounter: 0,

      // ── Anti-Double Tap (Frontend Cooldown) ───────────────
      // Map: rfid_uid → timestamp last tap (ms)
      lastTapMap: new Map(),
      COOLDOWN_MS: 30000,  // 30 detik cooldown per kartu

      // ── Web Audio Context ──────────────────────────────────
      audioCtx: null,

      // ── Debounce Hold Prevention ───────────────────────────
      bufferResetTimer: null,
      BUFFER_RESET_DELAY_MS: 500,
    };
  },

  mounted() {
    // Mulai clock
    this.updateClock();
    this.clockTimer = setInterval(this.updateClock, 1000);

    // Inisialisasi Web Audio
    this.initAudio();

    // Mulai polling jadwal (setiap 30 detik)
    this.fetchJadwalAktif();
    this.jadwalPollTimer = setInterval(this.fetchJadwalAktif, 30000);

    // Auto-focus input RFID
    this.$nextTick(() => this.focusInput());

    // Tangkap event keydown global untuk auto-refocus
    window.addEventListener('keydown', this.handleGlobalKeydown);
    document.addEventListener('visibilitychange', this.handleVisibilityChange);
  },

  beforeUnmount() {
    clearInterval(this.clockTimer);
    clearInterval(this.jadwalPollTimer);
    clearTimeout(this.popupTimer);
    clearTimeout(this.bufferResetTimer);
    window.removeEventListener('keydown', this.handleGlobalKeydown);
    document.removeEventListener('visibilitychange', this.handleVisibilityChange);
    if (this.audioCtx) this.audioCtx.close();
  },

  methods: {
    // ──────────────────────────────────────────────────────────
    // CLOCK
    // ──────────────────────────────────────────────────────────
    updateClock() {
      const now = new Date();
      this.currentTime = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
      });
      this.currentDate = now.toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
      });
    },

    // ──────────────────────────────────────────────────────────
    // AUDIO (Web Audio API — Native Browser, No Library)
    // ──────────────────────────────────────────────────────────
    initAudio() {
      try {
        this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      } catch (e) {
        console.warn('Web Audio API tidak didukung browser ini.');
      }
    },

    playBeepSuccess() {
      if (!this.audioCtx) return;
      const resume = this.audioCtx.state === 'suspended'
        ? this.audioCtx.resume() : Promise.resolve();
      resume.then(() => {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, this.audioCtx.currentTime);
        osc.frequency.setValueAtTime(1175, this.audioCtx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.4, this.audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.3);
        osc.start(this.audioCtx.currentTime);
        osc.stop(this.audioCtx.currentTime + 0.35);
      });
    },

    playBuzzError() {
      if (!this.audioCtx) return;
      const resume = this.audioCtx.state === 'suspended'
        ? this.audioCtx.resume() : Promise.resolve();
      resume.then(() => {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(150, this.audioCtx.currentTime);
        gain.gain.setValueAtTime(0.4, this.audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.5);
        osc.start(this.audioCtx.currentTime);
        osc.stop(this.audioCtx.currentTime + 0.55);
      });
    },

    // ──────────────────────────────────────────────────────────
    // FOCUS MANAGEMENT
    // ──────────────────────────────────────────────────────────
    focusInput() {
      this.$refs.rfidInput?.focus();
    },

    refocusInput() {
      setTimeout(() => this.focusInput(), 50);
    },

    scheduledRefocus() {
      this.isInputFocused = false;
      setTimeout(() => {
        if (document.activeElement !== this.$refs.rfidInput) {
          this.focusInput();
        }
      }, 200);
    },

    handleGlobalKeydown(e) {
      if (document.activeElement !== this.$refs.rfidInput) {
        this.focusInput();
      }
      this.isInputFocused = true;
    },

    handleVisibilityChange() {
      if (!document.hidden) {
        setTimeout(() => this.focusInput(), 500);
      }
    },

    // ──────────────────────────────────────────────────────────
    // CORE: PROSES RFID UID DARI READER
    // ──────────────────────────────────────────────────────────
    onEnterPressed() {
      const rawInput = this.rfidBuffer.trim();
      this.rfidBuffer = ''; 

      if (!rawInput || this.isProcessing) return;

      const uid = this.extractValidUID(rawInput);

      if (!uid) {
        console.warn('Format UID tidak valid:', rawInput);
        this.showPopupError({
          errorTitle: 'Format Kartu Tidak Valid',
          message: 'Kartu tidak terbaca dengan benar. Coba tap ulang dengan cepat.',
        });
        this.playBuzzError();
        return;
      }

      if (this.isInCooldown(uid)) {
        return; 
      }

      this.setTapCooldown(uid);
      this.submitAbsensi(uid);
    },

    extractValidUID(rawInput) {
      const cleaned = rawInput.toLowerCase().replace(/[^0-9a-f]/g, '');
      if (!cleaned) return null;

      const match16 = cleaned.match(/^([0-9a-f]{16})+$/);
      if (match16) return cleaned.substring(0, 16);

      const match8 = cleaned.match(/^([0-9a-f]{8})+$/);
      if (match8) return cleaned.substring(0, 8);

      if (cleaned.length >= 16) return cleaned.substring(0, 16);
      if (cleaned.length >= 8) return cleaned.substring(0, 8);

      return null;
    },

    // ──────────────────────────────────────────────────────────
    // ANTI-DOUBLE TAP (Frontend Cooldown)
    // ──────────────────────────────────────────────────────────
    isInCooldown(uid) {
      const lastTap = this.lastTapMap.get(uid);
      if (!lastTap) return false;
      return (Date.now() - lastTap) < this.COOLDOWN_MS;
    },

    setTapCooldown(uid) {
      this.lastTapMap.set(uid, Date.now());
      setTimeout(() => this.lastTapMap.delete(uid), this.COOLDOWN_MS + 1000);
    },

    // ──────────────────────────────────────────────────────────
    // API CALL — PROSES ABSENSI (MENGGUNAKAN AXIOS)
    // ──────────────────────────────────────────────────────────
    async submitAbsensi(uid) {
      this.isProcessing = true;

      try {
        const response = await axios.post('/proses-absensi', {
          rfid_uid: uid,
          ruangan: this.ruangan,
        });

        const data = response.data;

        if (data.success) {
          // ✅ BERHASIL
          this.playBeepSuccess();
          this.showPopupSuccess(data);
          this.addToRecentList(data, uid);
        }

      } catch (error) {
        // ❌ GAGAL (Status Code 403, 404, 409, 422)
        this.playBuzzError();

        if (error.response && error.response.data) {
          this.handleErrorResponse(error.response.data);
        } else {
          console.error('Network error:', error);
          this.showPopupError({
            errorTitle: 'Koneksi Gagal',
            message: 'Tidak dapat terhubung ke server. Periksa jaringan.',
          });
        }
      } finally {
        this.isProcessing = false;
        setTimeout(() => this.focusInput(), 300);
      }
    },

    handleErrorResponse(data) {
      const errorMessages = {
        'kartu_tidak_terdaftar': {
          errorTitle: 'Kartu Tidak Terdaftar',
          message: 'Kartu RFID ini belum terdaftar di sistem. Hubungi admin.',
        },
        'tidak_ada_jadwal': {
          errorTitle: 'Tidak Ada Kelas Aktif',
          message: 'Tidak ada jadwal kuliah yang berlangsung saat ini.',
        },
        'bukan_peserta': {
          errorTitle: 'Bukan Peserta Kelas Ini',
          message: data.message || 'Anda tidak terdaftar di mata kuliah ini.',
          nama: data.mahasiswa?.nama,
          nim: data.mahasiswa?.nim,
        },
        'sudah_absen': {
          errorTitle: 'Sudah Absen',
          message: 'Anda sudah melakukan absensi untuk kelas ini hari ini.',
          nama: data.mahasiswa?.nama,
          nim: data.mahasiswa?.nim,
        },
      };

      const errorData = errorMessages[data.type] || {
        errorTitle: 'Gagal',
        message: data.message || 'Terjadi kesalahan pada sistem.',
      };

      this.showPopupError(errorData);
    },

    // ──────────────────────────────────────────────────────────
    // JADWAL POLLING (MENGGUNAKAN AXIOS)
    // ──────────────────────────────────────────────────────────
    async fetchJadwalAktif() {
      try {
        const response = await axios.get(`/api/jadwal-aktif/${this.ruanganSlug}`);
        this.jadwalAktif = response.data.jadwal || null;
      } catch (err) {
        console.warn('Gagal fetch jadwal:', err);
      }
    },

    // ──────────────────────────────────────────────────────────
    // POPUP MANAGEMENT
    // ──────────────────────────────────────────────────────────
    showPopupSuccess(data) {
      this.popupData = {
        type: 'berhasil',
        status: data.status,
        nama: data.mahasiswa?.nama,
        nim: data.mahasiswa?.nim,
        foto: data.mahasiswa?.foto,
        matkul: data.mata_kuliah,
        waktu: data.waktu_tap,
      };
      this.openPopup();
    },

    showPopupError(errorData) {
      this.popupData = {
        type: 'error',
        ...errorData,
      };
      this.openPopup();
    },

    openPopup() {
      clearTimeout(this.popupTimer);
      this.showPopup = true;
      this.popupTimer = setTimeout(() => {
        this.showPopup = false;
        this.focusInput();
      }, this.popupDuration);
    },

    // ──────────────────────────────────────────────────────────
    // RECENT LIST
    // ──────────────────────────────────────────────────────────
    addToRecentList(data, uid) {
      const item = {
        id: ++this.recentCounter,
        nama: data.mahasiswa?.nama || '—',
        nim: data.mahasiswa?.nim || uid,
        foto: data.mahasiswa?.foto || null,
        status: data.status,
        waktu: data.waktu_tap,
      };
      this.recentAbsensi = [item, ...this.recentAbsensi].slice(0, 8);
    },
  },
};
</script>

<style scoped>
/* ============================================================
   LAYOUT DASAR
   Tema: Industrial/Utilitarian — cocok untuk ruang kelas kampus
   Dark mode mengikuti sistem operasi
   ============================================================ */

* { box-sizing: border-box; margin: 0; padding: 0; }

.monitor-wrapper {
  min-height: 100vh;
  background: #0a0e1a;
  color: #e8eaf0;
  font-family: 'Courier New', 'Consolas', monospace;
  display: grid;
  grid-template-rows: auto auto 1fr auto;
  grid-template-areas:
    "header"
    "jadwal"
    "tap"
    "recent";
  gap: 0;
  cursor: default;
  user-select: none;
  overflow: hidden;
}

/* Input RFID tersembunyi — tidak terlihat tapi selalu terfokus */
.rfid-hidden-input {
  position: fixed;
  top: -9999px;
  left: -9999px;
  width: 1px;
  height: 1px;
  opacity: 0;
  pointer-events: none;
  border: none;
  outline: none;
  background: transparent;
  color: transparent;
  caret-color: transparent;
}

/* ── HEADER ─────────────────────────────────────────── */
.monitor-header {
  grid-area: header;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem 2rem 1rem;
  border-bottom: 1px solid #1e2540;
  background: #0d1224;
}

.header-left { display: flex; flex-direction: column; gap: 4px; }

.room-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #4a9eff;
  background: rgba(74, 158, 255, 0.1);
  border: 1px solid rgba(74, 158, 255, 0.3);
  padding: 4px 12px;
  border-radius: 3px;
  width: fit-content;
}

.header-title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 4px;
  color: #ffffff;
  text-transform: uppercase;
}

.header-subtitle {
  font-size: 11px;
  color: #4a5680;
  letter-spacing: 1px;
}

.header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.clock-display {
  font-size: 32px;
  font-weight: 700;
  color: #4a9eff;
  font-family: 'Courier New', monospace;
  letter-spacing: 2px;
}

.date-display {
  font-size: 12px;
  color: #4a5680;
  text-transform: capitalize;
}

.focus-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #4a5680;
  margin-top: 4px;
}

.focus-indicator.active { color: #4eff8a; }

.focus-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #4a5680;
}
.focus-indicator.active .focus-dot {
  background: #4eff8a;
  box-shadow: 0 0 8px #4eff8a;
  animation: pulse-dot 1.5s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* ── JADWAL SECTION ─────────────────────────────────── */
.jadwal-section {
  grid-area: jadwal;
  padding: 1.25rem 2rem;
  background: #0d1224;
  border-bottom: 1px solid #1e2540;
}

.jadwal-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.jadwal-badge {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
  padding: 4px 10px;
  border-radius: 3px;
  text-transform: uppercase;
}

.jadwal-badge.hadir-badge {
  background: rgba(78, 255, 138, 0.1);
  color: #4eff8a;
  border: 1px solid rgba(78, 255, 138, 0.3);
  animation: glow-green 2s ease-in-out infinite;
}

@keyframes glow-green {
  0%, 100% { box-shadow: 0 0 4px rgba(78,255,138,0.2); }
  50% { box-shadow: 0 0 12px rgba(78,255,138,0.5); }
}

.matkul-name {
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 1px;
}

.jadwal-meta {
  display: flex;
  gap: 1.5rem;
  margin-left: auto;
}

.jadwal-meta span {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #6b7db3;
}

.jadwal-kosong {
  display: flex;
  align-items: center;
  gap: 1rem;
  color: #2a3050;
}

.jadwal-kosong svg { color: #2a3050; }

.jadwal-kosong p {
  font-size: 15px;
  color: #3a4570;
}

.jadwal-kosong span {
  font-size: 12px;
  color: #2a3050;
}

/* ── TAP SECTION ────────────────────────────────────── */
.tap-section {
  grid-area: tap;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
}

.tap-animation {
  position: relative;
  width: 160px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.tap-ring {
  position: absolute;
  border-radius: 50%;
  border: 1px solid rgba(74, 158, 255, 0.25);
  animation: expand-ring 3s ease-out infinite;
}

.tap-ring.ring-1 { width: 100px; height: 100px; animation-delay: 0s; }
.tap-ring.ring-2 { width: 130px; height: 130px; animation-delay: 0.8s; }
.tap-ring.ring-3 { width: 160px; height: 160px; animation-delay: 1.6s; }

@keyframes expand-ring {
  0% { opacity: 0.6; transform: scale(0.8); }
  100% { opacity: 0; transform: scale(1); }
}

.tap-animation.processing .tap-ring {
  animation-duration: 0.6s;
  border-color: rgba(78, 255, 138, 0.5);
}

.tap-icon {
  width: 80px; height: 80px;
  background: #1e2540;
  border: 1px solid #2e3a60;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4a9eff;
  position: relative;
  z-index: 1;
}

.tap-animation.processing .tap-icon {
  color: #4eff8a;
  border-color: #4eff8a;
  animation: spin-card 1s linear infinite;
}

@keyframes spin-card {
  0% { transform: rotate(0deg); }
  25% { transform: rotate(5deg); }
  75% { transform: rotate(-5deg); }
  100% { transform: rotate(0deg); }
}

.tap-label {
  font-size: 16px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #3a4570;
}

.processing-label { color: #4eff8a; }

/* ── RECENT SECTION ─────────────────────────────────── */
.recent-section {
  grid-area: recent;
  padding: 1rem 2rem 1.5rem;
  background: #0d1224;
  border-top: 1px solid #1e2540;
}

.recent-title {
  font-size: 10px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #2e3a60;
  margin-bottom: 0.75rem;
}

.recent-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.recent-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  background: #111828;
  border: 1px solid #1e2540;
  border-radius: 4px;
  animation: slide-in-item 0.4s ease-out;
}

@keyframes slide-in-item {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}

.recent-item.hadir { border-left: 3px solid #4eff8a; }
.recent-item.terlambat { border-left: 3px solid #ffa94d; }

.recent-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  overflow: hidden;
  background: #1e2540;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: #4a9eff;
  flex-shrink: 0;
}

.recent-avatar img { width: 100%; height: 100%; object-fit: cover; }

.recent-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.recent-nama {
  font-size: 13px;
  font-weight: 600;
  color: #c8d0e8;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.recent-nim { font-size: 11px; color: #4a5680; }

.recent-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
}

.recent-status-badge {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  padding: 2px 8px;
  border-radius: 3px;
  text-transform: uppercase;
}

.recent-status-badge.hadir {
  background: rgba(78,255,138,0.1);
  color: #4eff8a;
}

.recent-status-badge.terlambat {
  background: rgba(255,169,77,0.1);
  color: #ffa94d;
}

.recent-time { font-size: 11px; color: #3a4570; }

.recent-empty {
  text-align: center;
  color: #2e3a60;
  font-size: 13px;
  padding: 1rem;
}

/* ── POPUP NOTIFIKASI ───────────────────────────────── */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.popup-card {
  background: #111828;
  border: 1px solid #2e3a60;
  border-radius: 8px;
  padding: 2.5rem 3rem;
  min-width: 380px;
  max-width: 480px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.popup-card.berhasil { border-color: #4eff8a; }
.popup-card.error { border-color: #ff4a6a; }

.popup-icon {
  width: 72px; height: 72px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.success-icon {
  background: rgba(78,255,138,0.1);
  border: 2px solid #4eff8a;
  color: #4eff8a;
}

.error-icon {
  background: rgba(255,74,106,0.1);
  border: 2px solid #ff4a6a;
  color: #ff4a6a;
}

.popup-photo-wrap {
  margin: 0 auto 1rem;
  width: 90px; height: 90px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #2e3a60;
}

.popup-photo { width: 100%; height: 100%; object-fit: cover; }

.popup-photo-placeholder {
  width: 100%; height: 100%;
  background: #1e2540;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: 700;
  color: #4a9eff;
}

.popup-status-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  padding: 4px 14px;
  border-radius: 3px;
  margin-bottom: 1rem;
}

.popup-status-badge.hadir {
  background: rgba(78,255,138,0.1);
  color: #4eff8a;
}

.popup-status-badge.terlambat {
  background: rgba(255,169,77,0.1);
  color: #ffa94d;
}

.popup-nama {
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 4px;
}

.popup-nim {
  font-size: 13px;
  color: #4a5680;
  margin-bottom: 8px;
}

.popup-matkul {
  font-size: 14px;
  color: #6b7db3;
  margin-bottom: 4px;
}

.popup-time {
  font-size: 20px;
  font-weight: 700;
  color: #4a9eff;
  letter-spacing: 2px;
}

.popup-error-title {
  font-size: 20px;
  font-weight: 700;
  color: #ff4a6a;
  margin-bottom: 0.75rem;
}

.popup-error-msg {
  font-size: 14px;
  color: #6b7db3;
  line-height: 1.6;
}

/* Progress bar penutup otomatis popup */
.popup-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #1e2540;
}

.popup-progress-bar {
  height: 100%;
  background: #4a9eff;
  animation: shrink-bar linear forwards;
  width: 100%;
}

.popup-card.berhasil .popup-progress-bar { background: #4eff8a; }
.popup-card.error .popup-progress-bar { background: #ff4a6a; }

@keyframes shrink-bar {
  from { width: 100%; }
  to { width: 0%; }
}

/* ── TRANSITIONS ─────────────────────────────────────── */
.popup-fade-enter-active,
.popup-fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.popup-fade-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.popup-fade-leave-to {
  opacity: 0;
  transform: scale(1.05);
}

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 768px) {
  .monitor-header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .header-right { align-items: flex-start; }
  .clock-display { font-size: 24px; }
  .jadwal-section { padding: 1rem; }
  .jadwal-card { flex-direction: column; align-items: flex-start; }
  .jadwal-meta { margin-left: 0; flex-direction: column; gap: 0.5rem; }
  .tap-section { padding: 1.5rem 1rem; }
  .recent-section { padding: 1rem; }
  .popup-card { min-width: unset; margin: 1rem; padding: 2rem 1.5rem; }
}
</style>