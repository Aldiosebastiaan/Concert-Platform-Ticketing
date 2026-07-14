<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <header class="bg-gray-800 shadow-md">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <NuxtLink to="/" class="text-2xl font-bold text-blue-500">ConcertTix</NuxtLink>
        <nav>
          <NuxtLink to="/events" class="text-gray-300 hover:text-white mx-2 transition-colors">Events</NuxtLink>
        </nav>
      </div>
    </header>
    <main>
      <NuxtPage />
    </main>
  </div>
</template>