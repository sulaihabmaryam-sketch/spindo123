<template>
  <header :class="darkMode ? 'bg-iron-950/95 border-iron-800' : 'bg-white/95 border-wms-border'" class="sticky top-0 z-50 border-b backdrop-blur">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6">
      <div class="flex h-16 items-center justify-between">
        
        <!-- Brand Wordmark -->
        <div class="flex items-center space-x-3">
          <span class="border-l-4 border-wms-blue pl-2 font-display text-base font-black tracking-tight text-wms-navy dark:text-white">SPINDO</span>
          <div :class="darkMode ? 'bg-iron-700' : 'bg-wms-border'" class="h-5 w-px"></div>
          <span :class="darkMode ? 'text-iron-400' : 'text-wms-muted'" class="font-mono text-[10px] font-semibold tracking-[0.16em]">WMS SC-U7 PIPE</span>
        </div>



        <!-- Right Controls -->
        <div class="flex items-center space-x-2">
          <!-- SIKUTA Sync Status -->
          <button
            v-if="syncInfo"
            @click="$emit('view-last-sync')"
            class="hidden items-center space-x-1.5 mr-1 sm:flex px-2 py-1 rounded transition hover:bg-iron-800/40 cursor-pointer border border-transparent hover:border-iron-700/60"
            role="status"
            aria-live="polite"
            title="Klik untuk melihat Laporan Sinkronisasi Terakhir"
          >
            <span class="h-1.5 w-1.5 rounded-full" :class="syncInfo.mode === 'live' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-400'"></span>
            <span :class="darkMode ? 'text-iron-400' : 'text-slate-500'" class="font-mono text-[9px] tracking-wider">{{ syncInfo.mode === 'live' ? 'SIKUTA LIVE' : 'MODE DEMO' }}</span>
            <span v-if="syncInfo.last_changes?.summary?.total_changes !== undefined" :class="syncInfo.last_changes.summary.total_changes > 0 ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-iron-800 text-iron-500 border border-iron-700/50'" class="ml-1 rounded px-1 text-[8px] font-mono border">
              {{ syncInfo.last_changes.summary.total_changes }} ubah
            </span>
          </button>
          <button
            @click="$emit('sync-sikuta')"
            :disabled="syncing"
            aria-label="Sinkronkan data SIKUTA"
            :class="darkMode ? 'text-iron-400 hover:bg-iron-800 hover:text-safety' : 'text-slate-500 hover:bg-slate-100 hover:text-amber-600'"
            class="relative border border-transparent p-2 transition hover:border-wms-border"
            title="Sinkronkan data SIKUTA"
          >
            <svg :class="syncing ? 'animate-spin' : ''" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            <span v-if="syncing" class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-safety animate-ping"></span>
          </button>
          <button
            @click="$emit('toggle-theme')"
            :class="darkMode ? 'text-iron-400 hover:bg-iron-800 hover:text-iron-200' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900'"
            class="border border-transparent p-2 transition hover:border-wms-border"
            :title="darkMode ? 'Gunakan light mode' : 'Gunakan dark mode'"
            :aria-label="darkMode ? 'Gunakan light mode' : 'Gunakan dark mode'"
          >
            <svg v-if="darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" stroke-width="2" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
          </button>
          <button
            @click="$emit('refresh')"
            :class="darkMode ? 'text-iron-400 hover:bg-iron-800 hover:text-iron-200' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900'"
            class="border border-transparent p-2 transition hover:border-wms-border"
            title="Muat ulang denah"
            aria-label="Muat ulang denah"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
          </button>
        </div>
      </div>


    </div>
  </header>
</template>

<script setup>
defineProps({
  darkMode: { type: Boolean, default: false },
  syncInfo: { type: Object, default: null },
  syncing: { type: Boolean, default: false },
});

defineEmits(['refresh', 'toggle-theme', 'sync-sikuta', 'view-last-sync']);
</script>
