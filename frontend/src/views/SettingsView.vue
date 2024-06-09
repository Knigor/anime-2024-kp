<template>
  <section class="flex flex-col gap-6 w-full pb-16">
    <div class="flex gap-8">
      <div class="flex flex-col gap-8 w-full">
        <!-- Личная информация и безопасность -->
        <div class="flex gap-8 w-full">
          <!-- Личная информация -->
          <div class="flex gap-6 flex-col w-full">
            <div class="flex justify-between items-center">
              <h3 class="font-bold">Личная информация</h3>
              <Button @click="saveData">Сохранить</Button>
            </div>
            <div class="flex flex-col gap-3">
              <div class="flex gap-4 items-center">
                <p class="w-[112px]">Логин</p>
                <Input type="text" :placeholder="item.full_name_user" v-model="fio" />
              </div>
              <div class="flex gap-4 items-center">
                <p class="w-[112px]">Почта</p>
                <Input type="text" :placeholder="item.email_user" v-model="email" />
              </div>
            </div>
          </div>
          <!-- Безопасность -->
          <div class="flex gap-6 flex-col w-full">
            <div class="flex justify-between items-center">
              <h3 class="font-bold">Безопасность</h3>
              <Button @click="setNewPassword">Обновить</Button>
            </div>
            <div class="flex flex-col gap-3">
              <div class="flex gap-4 items-center">
                <p class="w-[112px]">Текущий пароль</p>
                <Input type="password" placeholder="Ваш текущий пароль..." v-model="oldPassword" />
              </div>
              <div class="flex gap-4 items-center">
                <p class="w-[112px]">Новый пароль</p>
                <Input type="password" placeholder="Ваш новый пароль..." v-model="newPassword" />
              </div>
            </div>
          </div>
        </div>
        <!-- Изм фото -->
        <div class="flex flex-wrap">
          <div class="flex flex-col gap-4">
            <h3>Изменение фото</h3>
            <Avatar class="w-[128px] h-[128px] rounded-2xl">
              <AvatarImage :src="`http://localhost:8080/img/${item.photo_user}`" />
            </Avatar>

            <div class="flex items-center justify-center w-full">
              <label
                for="cover-file"
                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
              >
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                  <svg
                    class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 16"
                  >
                    <path
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                    />
                  </svg>
                  <p
                    class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                    style="text-align: center"
                  >
                    <span class="font-semibold">Перетащите аватарку в это окно</span><br />
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                    (только форматы .png, .jpg)
                  </p>
                  <p
                    class="mt-3 text-sm text-gray-500 dark:text-gray-400"
                    style="text-align: center"
                  >
                    <span class="font-semibold"><em>Для выбора нажмите на это окно.</em></span>
                  </p>
                </div>
                <input
                  @change="handleCoverUpload"
                  id="cover-file"
                  type="file"
                  class="hidden"
                  accept=".png,.jpg"
                />
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'

import { Input } from '@/components/ui/input'

import { ref, onMounted } from 'vue'

import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { cn } from '@/lib/utils'
import axios from 'axios'
import { Checkbox } from '@/components/ui/checkbox'

const item = ref({})

// ниже данные на обновление

const coverImage = ref(null)
const value = ref()
const fio = ref()
const email = ref()
const phone = ref()
const oldPassword = ref()
const newPassword = ref()
const photoName = ref(null)

const handleCoverUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    coverImage.value = file
    photoName.value = file.name
    console.log('Выбранное изображение:', file.name)
  }
}

const setNewPassword = async () => {
  const formData = new FormData()
  formData.append('id_user', localStorage.id_user)
  formData.append('old_password', oldPassword.value)
  formData.append('new_password', newPassword.value)

  try {
    const response = await axios.post('http://localhost:8080/update_user.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    console.log(response.data)

    if (response.data && response.data.error) {
      alert('Введен неправильный старый пароль.')
    } else {
      alert('Пароль изменен')
    }

    getProfile()
  } catch (error) {
    console.error('Ошибка при отправке данных:', error)
  }
}

// снизу обновляется личная информация

const saveData = async () => {
  const formData = new FormData()
  formData.append('id_user', localStorage.id_user)

  if (fio.value) {
    formData.append('full_name', fio.value)
    localStorage.setItem('full_name', fio.value)
  }
  if (email.value) {
    formData.append('email', email.value)
  }
  if (value.value) {
    formData.append('birth', value.value)
  }
  if (phone.value) {
    formData.append('phone', phone.value)
  }
  if (coverImage.value) {
    formData.append('photo', coverImage.value)
  }

  for (var pair of formData.entries()) {
    console.log(pair[0] + ': ' + pair[1])
  }

  try {
    const response = await axios.post('http://localhost:8080/update_user.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    console.log(response.data)

    // localStorage.setItem('photo_user', response.data.photo_user)

    getProfile()
  } catch (error) {
    console.error('Ошибка при отправке данных:', error)
  }
}

const getProfile = async () => {
  const formData = new FormData()
  formData.append('id_user', localStorage.id_user)

  console.log(localStorage.id_user)
  try {
    const response = await axios.post('http://localhost:8080/show_profile.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    item.value = response.data

    localStorage.setItem('photo_user', response.data.photo_user)

    console.log(response.data)
  } catch (error) {
    console.error('Ошибка:', error)
  }
}

onMounted(getProfile)
</script>
