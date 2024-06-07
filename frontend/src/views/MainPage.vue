<template>
  <div class="flex flex-col justify-center">
    <div class="flex items-start justify-start">
      <div class="flex items-center justify-between gap-4 mt-4 ml-16 i">
        <p>{{ user }}</p>
        <Button v-if="isvisible" @click="addBook" class="w-[120px]" variant="link"
          >Добавить книгу</Button
        >
        <Button v-if="isvisible" @click="goToAuth" class="w-[10px]" variant="link">Выйти</Button>
        <Button v-else @click="goToAuth" class="w-[10px]" variant="link">Войти</Button>
        <Button @click="goToGeo" class="w-[120px]" variant="link">Геолокация</Button>
      </div>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-12 mb-8 mt-8">
      <div
        v-for="item in items"
        :key="item.id_book"
        class="border-2 border-slate w-[300px] h-[350px] flex flex-col relative"
      >
        <div class="flex flex-col ml-4 mt-4">
          <img
            class="w-[100px] h-[150px]"
            :src="`http://localhost/view/images/${item.cover_image}`"
          />
          <div class="flex flex-col">
            <p class="font-semibold text-rose-400">{{ item.title }}</p>
            <div class="flex gap-2">
              <p class="font-sm text-slate-500">Автор:</p>
              <p>{{ item.author }}</p>
            </div>
            <div v-if="isvisible" class="flex flex-wrap gap-4 absolute bottom-0 left-0 ml-4">
              <router-link :to="{ path: `/editBooks/${item.id_book}` }"
                ><Button class="w-[100px] text-orange-400" variant="link"
                  >Редактировать</Button
                ></router-link
              >

              <Button @click="deleteBook(item.id_book)" class="w-[80px] text-red-700" variant="link"
                >Удалить</Button
              >
            </div>
          </div>
        </div>
        <Button
          @click="downloadBook(item.id_book)"
          v-if="(isDownload = item.allow_download)"
          class="w-[80px] ml-1 text-green-600"
          variant="link"
          >Скачать</Button
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()

const addBook = () => {
  router.push('/addBooks')
}

const goToAuth = () => {
  router.push('/auth')
}

const goToGeo = () => {
  router.push('/geoData')
}

// удаляем книгу

const deleteBook = async (id) => {
  const formData = new FormData()
  formData.append('id', id)

  console.log(id)

  try {
    const response = await axios.post('http://localhost/deleteBook', formData)

    console.log(response.data)

    booksFetch()
  } catch (error) {
    console.error('Ошибка при скачивании файла:', error)
  }
}

// скачиваем книгу

const downloadBook = async (id) => {
  const formData = new FormData()
  formData.append('id', id)

  console.log(id)
  try {
    const response = await axios.post('http://localhost/downloadBook', formData, {
      responseType: 'Blob'
    })

    console.log(response.data)

    console.log(response.headers)

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'file.txt') // Получаем имя файла из заголовка
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Ошибка при скачивании файла:', error)
  }
}

const items = ref([])

const booksFetch = async () => {
  try {
    const response = await axios.get('http://localhost/books')
    items.value = response.data

    console.log(items.value)
  } catch (err) {
    console.error(err)
  }
}
console.log(localStorage)

onMounted(booksFetch)

const user = ref('')
const isvisible = ref(true)

if (localStorage.length !== 0) {
  isvisible.value = true
  user.value = localStorage.login
} else {
  isvisible.value = false
}
</script>

<style lang="scss" scoped></style>
