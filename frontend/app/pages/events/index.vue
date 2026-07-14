<script setup>
const config = useRuntimeConfig()
const { data: eventsResponse, pending, error } = await useFetch(`${config.public.apiBaseUrl}/api/events`)
const events = computed(() => eventsResponse.value?.data || [])
</script>

<template>
  <div class="container mx-auto p-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-white">Daftar Event</h1>
    
    <div v-if="pending" class="text-white">
      Loading events...
    </div>
    <div v-else-if="error" class="text-red-500">
      Failed to load events.
    </div>
    <div v-else-if="events.length === 0" class="text-gray-400">
      Tidak ada event tersedia saat ini.
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="event in events" :key="event.id" class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transition-transform hover:scale-105">
        <img :src="event.gambar_url" :alt="event.judul" class="w-full h-48 object-cover" />
        <div class="p-4">
          <div class="flex justify-between items-start mb-2">
            <h2 class="text-xl font-bold text-white">{{ event.judul }}</h2>
            <span class="bg-blue-600 text-xs px-2 py-1 rounded text-white">{{ event.kategori?.nama }}</span>
          </div>
          <p class="text-gray-400 text-sm mb-4">{{ event.lokasi }}</p>
          <p class="text-gray-300 text-sm mb-4 line-clamp-2">{{ event.deskripsi }}</p>
          
          <div class="flex justify-between items-center border-t border-gray-700 pt-4">
            <span class="text-sm text-gray-400 font-bold text-blue-400">
              Mulai dari: Rp {{ event.tikets.length > 0 ? event.tikets[0].harga : '-' }}
            </span>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
              Lihat Detail
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
