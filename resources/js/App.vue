<template>
  <div :data-theme="darkMode ? 'dark' : 'light'" :class="darkMode ? 'bg-iron-950 text-iron-300' : 'bg-wms-bg text-wms-ink'" class="min-h-screen font-display antialiased transition-colors">
    
    <Navbar
      :dark-mode="darkMode"
      :sync-info="syncInfo"
      :syncing="syncing"
      @refresh="loadMapData"
      @toggle-theme="toggleTheme"
      @sync-sikuta="handleSyncSikuta"
      @view-last-sync="openLastSyncReport"
    />

    <!-- Toast notification -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div
        v-if="toastMessage"
        class="fixed bottom-5 right-5 z-50 max-w-[min(24rem,calc(100vw-2rem))] border-l-4 border-wms-blue bg-wms-navy px-4 py-3 text-xs font-medium text-white shadow-md"
      >
        {{ toastMessage }}
      </div>
    </Transition>

    <!-- Sync Detail Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="syncDetail" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/75 backdrop-blur-sm p-3 sm:p-4" @click.self="syncDetail = null">
        <div class="relative w-full max-w-3xl border border-iron-700 bg-iron-900 text-iron-200 shadow-2xl flex flex-col max-h-[90vh]">
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-iron-800 px-5 py-3.5 bg-iron-950/70">
            <div class="flex items-center space-x-2.5">
              <span class="flex h-7 w-7 items-center justify-center rounded border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 font-mono text-xs">
                🔄
              </span>
              <div>
                <h3 class="font-display text-sm font-bold tracking-wide text-white">
                  {{ syncDetail.success ? 'LAPORAN SINKRONISASI SIKUTA' : 'STATUS SINKRONISASI' }}
                </h3>
                <p class="text-[10px] font-mono text-iron-400">
                  {{ syncDetail.changes?.synced_at ? new Date(syncDetail.changes.synced_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : (syncDetail.message || 'Data Terkini') }}
                </p>
              </div>
            </div>
            <button @click="syncDetail = null" class="text-iron-400 hover:text-white transition p-1" title="Tutup">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Navigation Tabs -->
          <div class="flex border-b border-iron-800 bg-iron-950/40 px-5 text-xs font-mono">
            <button
              @click="activeSyncTab = 'changes'"
              :class="activeSyncTab === 'changes' ? 'border-b-2 border-wms-blue text-white font-bold' : 'text-iron-400 hover:text-iron-200'"
              class="flex items-center space-x-2 py-2.5 px-3 transition cursor-pointer"
            >
              <span>📋 Perubahan Data</span>
              <span
                v-if="syncDetail.changes?.summary?.total_changes !== undefined"
                :class="syncDetail.changes.summary.total_changes > 0 ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30 font-bold' : 'bg-iron-800 text-iron-400'"
                class="rounded-full px-1.5 py-0.2 text-[10px] border"
              >
                {{ syncDetail.changes.summary.total_changes }}
              </span>
            </button>
            <button
              @click="activeSyncTab = 'summary'"
              :class="activeSyncTab === 'summary' ? 'border-b-2 border-wms-blue text-white font-bold' : 'text-iron-400 hover:text-iron-200'"
              class="flex items-center space-x-2 py-2.5 px-3 transition cursor-pointer"
            >
              <span>📊 Kondisi Total Gudang</span>
            </button>
          </div>

          <!-- Content Body (Scrollable) -->
          <div class="px-5 py-4 space-y-4 overflow-y-auto flex-1 text-xs">
            
            <!-- TAB 1: PERUBAHAN DATA (CHANGELOG) -->
            <div v-if="activeSyncTab === 'changes'" class="space-y-4">
              
              <!-- Summary Statistics Bar -->
              <div v-if="syncDetail.changes?.summary" class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                <div class="border border-emerald-500/20 bg-emerald-950/20 px-3 py-2">
                  <span class="text-[10px] text-emerald-400/80 font-mono">Baru Masuk</span>
                  <p class="text-base font-bold text-emerald-400">+{{ syncDetail.changes.summary.added_count }}</p>
                </div>
                <div class="border border-sky-500/20 bg-sky-950/20 px-3 py-2">
                  <span class="text-[10px] text-sky-400/80 font-mono">Bertambah</span>
                  <p class="text-base font-bold text-sky-400">+{{ syncDetail.changes.summary.increased_count }}</p>
                </div>
                <div class="border border-amber-500/20 bg-amber-950/20 px-3 py-2">
                  <span class="text-[10px] text-amber-400/80 font-mono">Berkurang</span>
                  <p class="text-base font-bold text-amber-400">-{{ syncDetail.changes.summary.decreased_count }}</p>
                </div>
                <div class="border border-rose-500/20 bg-rose-950/20 px-3 py-2">
                  <span class="text-[10px] text-rose-400/80 font-mono">Habis / Keluar</span>
                  <p class="text-base font-bold text-rose-400">-{{ syncDetail.changes.summary.removed_count }}</p>
                </div>
                <div class="border border-iron-700 bg-iron-800/60 px-3 py-2 col-span-2 sm:col-span-1">
                  <span class="text-[10px] text-iron-400 font-mono">Net Perubahan</span>
                  <p class="text-xs font-bold font-mono" :class="syncDetail.changes.summary.net_pcs >= 0 ? 'text-emerald-400' : 'text-rose-400'">
                    {{ syncDetail.changes.summary.net_pcs >= 0 ? '+' : '' }}{{ Number(syncDetail.changes.summary.net_pcs).toLocaleString('id-ID') }} pcs
                  </p>
                  <span class="text-[10px] font-mono text-iron-400">
                    {{ syncDetail.changes.summary.net_ton >= 0 ? '+' : '' }}{{ syncDetail.changes.summary.net_ton }} ton
                  </span>
                </div>
              </div>

              <!-- Filter & Search Toolbar -->
              <div v-if="syncDetail.changes?.summary?.total_changes > 0" class="flex flex-wrap items-center justify-between gap-2 border-b border-iron-800 pb-3">
                <div class="flex flex-wrap items-center gap-1.5">
                  <button
                    v-for="btn in filterButtons"
                    :key="btn.id"
                    @click="changeFilter = btn.id"
                    :class="changeFilter === btn.id ? 'bg-wms-blue text-white font-semibold shadow' : 'bg-iron-800/80 text-iron-400 hover:text-iron-200 border border-iron-700/50'"
                    class="rounded px-2.5 py-1 text-[11px] font-mono transition cursor-pointer"
                  >
                    {{ btn.label }}
                  </button>
                </div>
                <div class="relative w-full sm:w-56">
                  <input
                    v-model="changeSearch"
                    type="text"
                    placeholder="Cari blok / material..."
                    class="w-full bg-iron-800/80 border border-iron-700 rounded px-2.5 py-1 text-xs text-white placeholder-iron-500 focus:outline-none focus:border-wms-blue"
                  />
                  <button v-if="changeSearch" @click="changeSearch = ''" class="absolute right-2 top-1 text-iron-400 hover:text-white cursor-pointer">&times;</button>
                </div>
              </div>

              <!-- Changes List / Table -->
              <div v-if="filteredChanges.length > 0" class="border border-iron-800 rounded overflow-hidden">
                <div class="max-h-80 overflow-y-auto divide-y divide-iron-800/60">
                  <div
                    v-for="(item, idx) in filteredChanges"
                    :key="idx"
                    class="p-3 hover:bg-iron-800/40 transition flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                  >
                    <!-- Left: Badge, Rack, Pipe Easy Name -->
                    <div class="flex items-start space-x-3 min-w-0 flex-1">
                      <!-- Badge Type -->
                      <span
                        :class="{
                          'bg-emerald-500/10 text-emerald-400 border-emerald-500/30': item.type === 'added',
                          'bg-sky-500/10 text-sky-400 border-sky-500/30': item.type === 'increased',
                          'bg-amber-500/10 text-amber-400 border-amber-500/30': item.type === 'decreased',
                          'bg-rose-500/10 text-rose-400 border-rose-500/30': item.type === 'removed'
                        }"
                        class="shrink-0 border px-1.5 py-0.5 rounded text-[9px] font-mono font-bold uppercase tracking-wider"
                      >
                        {{ item.type_label }}
                      </span>

                      <!-- Rack Code -->
                      <div class="shrink-0">
                        <span class="font-mono font-bold text-amber-400 bg-amber-950/30 border border-amber-500/20 px-1.5 py-0.5 rounded text-xs">
                          {{ item.rack_code }}
                        </span>
                        <div class="text-[9px] text-iron-500 font-mono mt-0.5">{{ item.gudang }}</div>
                      </div>

                      <!-- Pipe Info -->
                      <div class="min-w-0 flex-1">
                        <p class="font-semibold text-white text-xs truncate" :title="item.nama_mudah">
                          {{ item.nama_mudah }}
                        </p>
                        <p class="text-[11px] font-mono text-iron-400 truncate mt-0.5" :title="item.description">
                          <span class="text-iron-500">{{ item.material_code }}</span>
                          <span v-if="item.description"> &bull; {{ item.description }}</span>
                        </p>
                      </div>
                    </div>

                    <!-- Right: Qty & Weight Transition -->
                    <div class="flex items-center sm:text-right space-x-4 sm:space-x-6 shrink-0 pt-1 sm:pt-0 border-t sm:border-t-0 border-iron-800">
                      <div>
                        <div class="text-[10px] text-iron-500 font-mono">Batang (Pcs)</div>
                        <div class="font-mono text-xs text-white">
                          <span class="text-iron-400">{{ Number(item.old_pcs).toLocaleString('id-ID') }}</span>
                          <span class="text-iron-600 mx-1">&rarr;</span>
                          <span class="font-bold">{{ Number(item.new_pcs).toLocaleString('id-ID') }}</span>
                          <span
                            :class="item.diff_pcs > 0 ? 'text-emerald-400' : 'text-rose-400'"
                            class="ml-1 text-[11px] font-bold"
                          >
                            ({{ item.diff_pcs > 0 ? '+' : '' }}{{ Number(item.diff_pcs).toLocaleString('id-ID') }})
                          </span>
                        </div>
                      </div>

                      <div>
                        <div class="text-[10px] text-iron-500 font-mono">Tonase</div>
                        <div class="font-mono text-xs text-white">
                          <span class="text-iron-400">{{ item.old_ton }}t</span>
                          <span class="text-iron-600 mx-1">&rarr;</span>
                          <span class="font-bold">{{ item.new_ton }}t</span>
                          <span
                            :class="item.diff_ton > 0 ? 'text-emerald-400' : 'text-rose-400'"
                            class="ml-1 text-[10px]"
                          >
                            ({{ item.diff_ton > 0 ? '+' : '' }}{{ item.diff_ton }}t)
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty Filter State -->
              <div v-else-if="syncDetail.changes?.summary?.total_changes > 0" class="text-center py-6 text-iron-500 border border-dashed border-iron-800 rounded">
                Tidak ada perubahan yang cocok dengan filter atau pencarian "{{ changeSearch }}".
              </div>

              <!-- Zero Changes State (Identical) -->
              <div v-else class="border border-emerald-500/30 bg-emerald-950/20 rounded p-6 text-center">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 text-lg mb-2">
                  ✓
                </div>
                <h4 class="text-sm font-bold text-emerald-300">Tidak Ada Perubahan Data Stok</h4>
                <p class="text-xs text-iron-400 mt-1 max-w-md mx-auto">
                  Seluruh data stok pipa dan rak di WMS sudah identik & sama persis dengan data di SIKUTA AppSheet (Semua {{ syncDetail.d?.stok_synced ?? 0 }} baris cocok).
                </p>
              </div>

            </div>

            <!-- TAB 2: KONDISI TOTAL GUDANG -->
            <div v-else-if="activeSyncTab === 'summary'" class="space-y-4">
              <!-- Sync Counts -->
              <div>
                <p class="text-[10px] font-mono uppercase tracking-widest text-iron-500 mb-2">Data yang Disinkronkan</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Gudang</span>
                    <p class="text-lg font-bold text-emerald-400">{{ syncDetail.d?.gudang_synced ?? 0 }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Blok</span>
                    <p class="text-lg font-bold text-emerald-400">{{ syncDetail.d?.blok_synced ?? 0 }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Produk/Material</span>
                    <p class="text-lg font-bold text-sky-400">{{ syncDetail.d?.produk_synced ?? 0 }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Baris SIKUTA</span>
                    <p class="text-lg font-bold text-sky-400">{{ syncDetail.d?.stok_synced ?? 0 }}</p>
                  </div>
                </div>
              </div>

              <!-- Database Stats -->
              <div v-if="syncDetail.d?.total_stok_pcs > 0">
                <p class="text-[10px] font-mono uppercase tracking-widest text-iron-500 mb-2">Kondisi Database Sekarang</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Blok Terisi / Total</span>
                    <p class="text-base font-bold text-amber-400">{{ syncDetail.d.total_blok_terisi }} / {{ syncDetail.d.total_blok }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Jenis Material</span>
                    <p class="text-base font-bold text-amber-400">{{ syncDetail.d.total_jenis_material }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Total Stok (pcs)</span>
                    <p class="text-base font-bold text-white">{{ Number(syncDetail.d.total_stok_pcs).toLocaleString('id-ID') }}</p>
                  </div>
                  <div class="border border-iron-700/50 bg-iron-800/50 px-3 py-2">
                    <span class="text-[10px] text-iron-500 font-mono">Total Tonase</span>
                    <p class="text-base font-bold text-white">{{ Number(syncDetail.d.total_tonase_ton).toLocaleString('id-ID', {minimumFractionDigits:2}) }} ton</p>
                  </div>
                </div>
              </div>

              <!-- Top Materials -->
              <div v-if="syncDetail.d?.top_materials?.length > 0">
                <p class="text-[10px] font-mono uppercase tracking-widest text-iron-500 mb-2">Top 5 Material (Stok Terbanyak)</p>
                <div class="border border-iron-700/50 overflow-hidden rounded">
                  <table class="w-full text-xs">
                    <thead>
                      <tr class="bg-iron-800 text-iron-400">
                        <th class="px-3 py-1.5 text-left font-mono">#</th>
                        <th class="px-3 py-1.5 text-left font-mono">Kode Material</th>
                        <th class="px-3 py-1.5 text-right font-mono">Pcs</th>
                        <th class="px-3 py-1.5 text-right font-mono">Ton</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(mat, i) in syncDetail.d.top_materials" :key="i" class="border-t border-iron-800">
                        <td class="px-3 py-1.5 text-iron-500">{{ i + 1 }}</td>
                        <td class="px-3 py-1.5 font-mono text-iron-300 text-[10px]">{{ mat.material }}</td>
                        <td class="px-3 py-1.5 text-right font-mono text-emerald-400">{{ Number(mat.pcs).toLocaleString('id-ID') }}</td>
                        <td class="px-3 py-1.5 text-right font-mono text-sky-400">{{ mat.ton }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Warning if no data -->
            <div v-if="!syncDetail.success" class="bg-amber-900/30 border border-amber-700/50 px-3 py-2 text-xs text-amber-300">
              ⚠️ {{ syncDetail.message }}
            </div>
          </div>

          <!-- Footer -->
          <div class="border-t border-iron-800 px-5 py-3 flex items-center justify-between bg-iron-950/50">
            <span class="text-[11px] text-iron-500 font-mono">
              PT SPINDO Tbk &bull; Unit SC-U7
            </span>
            <button @click="syncDetail = null" class="bg-wms-blue px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-600 transition rounded shadow cursor-pointer">
              Tutup Laporan
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <main class="mx-auto max-w-screen-2xl px-4 py-5 sm:px-6 sm:py-7">
      <div>
        <BlueprintWarehouseMap ref="warehouseMap" />
      </div>
    </main>

    <footer class="mt-12 border-t border-wms-border py-5 text-center text-[11px] font-mono text-wms-muted dark:border-iron-800 dark:text-iron-600">
      PT Steel Pipe Industry of Indonesia Tbk (SPINDO) &mdash; WMS SC-U7 &copy; 2026
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Navbar from './components/Navbar.vue';
import BlueprintWarehouseMap from './components/BlueprintWarehouseMap.vue';

const toastMessage = ref('');
const darkMode = ref(false);
const syncInfo = ref(null);
const syncing = ref(false);
const warehouseMap = ref(null);
const syncDetail = ref(null);

const activeSyncTab = ref('changes');
const changeFilter = ref('all');
const changeSearch = ref('');

const filterButtons = computed(() => {
  const summary = syncDetail.value?.changes?.summary;
  return [
    { id: 'all', label: `Semua (${summary?.total_changes ?? 0})` },
    { id: 'added', label: `Baru (+${summary?.added_count ?? 0})` },
    { id: 'increased', label: `Bertambah (+${summary?.increased_count ?? 0})` },
    { id: 'decreased', label: `Berkurang (-${summary?.decreased_count ?? 0})` },
    { id: 'removed', label: `Habis (-${summary?.removed_count ?? 0})` },
  ];
});

const filteredChanges = computed(() => {
  const items = syncDetail.value?.changes?.items || [];
  let res = items;
  if (changeFilter.value !== 'all') {
    res = res.filter(item => item.type === changeFilter.value);
  }
  if (changeSearch.value && changeSearch.value.trim()) {
    const q = changeSearch.value.toLowerCase().trim();
    res = res.filter(item => 
      (item.rack_code && item.rack_code.toLowerCase().includes(q)) ||
      (item.material_code && item.material_code.toLowerCase().includes(q)) ||
      (item.nama_mudah && item.nama_mudah.toLowerCase().includes(q)) ||
      (item.description && item.description.toLowerCase().includes(q)) ||
      (item.gudang && item.gudang.toLowerCase().includes(q))
    );
  }
  return res;
});

function toggleTheme() {
  darkMode.value = !darkMode.value;
  localStorage.setItem('wms-theme', darkMode.value ? 'dark' : 'light');
}

function showToast(msg) {
  toastMessage.value = msg;
  setTimeout(() => { toastMessage.value = ''; }, 3500);
}

async function loadMapData() {
  if (warehouseMap.value) {
    await warehouseMap.value.loadMap();
  }
}

async function loadSyncStatus() {
  try {
    const res = await fetch('/api/wms/sync-status');
    const json = await res.json();
    if (json.status === 'success') {
      syncInfo.value = json.data;
    }
  } catch (err) { console.error('Sync status error:', err); }
}

function openLastSyncReport() {
  if (syncInfo.value?.last_changes) {
    syncDetail.value = {
      success: true,
      message: 'Laporan Sinkronisasi Terakhir',
      d: syncDetail.value?.d || {},
      changes: syncInfo.value?.last_changes
    };
    activeSyncTab.value = 'changes';
    changeFilter.value = 'all';
    changeSearch.value = '';
  } else {
    showToast('ℹ️ Belum ada riwayat perubahan data tersimpan. Silakan lakukan sinkronisasi.');
  }
}

async function handleSyncSikuta() {
  syncing.value = true;
  showToast('⏳ Sinkronisasi SIKUTA dimulai... mohon tunggu.');
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    };
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    const res = await fetch('/api/wms/sync', {
      method: 'POST',
      headers,
      body: JSON.stringify({ table: 'stok' })
    });
    let json = null;
    try {
      json = await res.json();
    } catch {
      throw new Error(`Server mengembalikan HTTP ${res.status}`);
    }

    if (res.ok && json.status === 'success') {
      syncDetail.value = {
        success: true,
        message: json.message,
        d: json.data?.detail || {},
        changes: json.data?.changes || null
      };
      activeSyncTab.value = 'changes';
      changeFilter.value = 'all';
      changeSearch.value = '';
      await loadMapData();
      await loadSyncStatus();
    } else if (json?.status === 'warning') {
      syncDetail.value = {
        success: false,
        message: json.message,
        d: json.data?.detail || {},
        changes: json.data?.changes || null
      };
      activeSyncTab.value = 'changes';
      await loadMapData();
      await loadSyncStatus();
    } else {
      showToast('⚠️ ' + (json?.message || 'Gagal sinkronisasi SIKUTA.'));
    }
  } catch (err) {
    console.error('Sync error:', err);
    showToast('❌ ' + (err.message || 'Koneksi server bermasalah.'));
  } finally {
    syncing.value = false;
  }
}

onMounted(() => {
  darkMode.value = localStorage.getItem('wms-theme') === 'dark';
  loadSyncStatus();
});
</script>
