<template>
  <div class="flex items-center justify-center h-screen gap-24">
    <div class="relative hidden h-full flex-col bg-muted p-10 text-white dark:border-r lg:flex">
      <div class="absolute inset-0 bg-image"></div>

      <div class="relative z-20 flex items-center text-lg font-medium">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
          class="mr-2 h-6 w-6"
        >
          <path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3"></path>
        </svg>
        Medical
      </div>
      <div class="relative z-20 mt-auto">
        <blockquote class="space-y-2">
          <p class="text-lg">
            “Стратегия без тактики — это самый медленный путь к победе. Тактика без стратегии — это
            просто суета перед поражением.”
          </p>
          <footer class="text-sm">Сунь Цзы</footer>
        </blockquote>
      </div>
    </div>

    <div class="flex flex-col items-center gap-4 mr-12">
      <h1 class="font-bold">Войдите</h1>
      <div class="border-t h-5 w-96 border-purple-600"></div>

      <div class="grid w-full max-w-sm items-center gap-1.5">
        <form class="w-96 space-y-6" @submit="onSubmit">
          <FormField v-slot="{ componentField: LoginField }" name="login">
            <FormItem>
              <FormLabel>Логин</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Введите ваш логин" v-bind="LoginField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField: PasswordField }" name="password">
            <FormItem>
              <FormLabel>Пароль</FormLabel>
              <FormControl>
                <Input type="password" placeholder="Введите ваш пароль" v-bind="PasswordField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <div class="flex flex-col">
            <Toaster />
            <div class="flex">
              <Button type="submit" class="bg-purple-500 w-32 text-white mr-5"> Войти </Button>
              <Button @click="goToRegister" class="w-64 bg-slate-400"> Зарегистрироваться </Button>
            </div>

            <Button @click="goToMain" class="w-[50px] ml-36 mt-2" variant="link">На главную</Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { Toaster } from '@/components/ui/toast'
import { useToast } from '@/components/ui/toast/use-toast'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import * as z from 'zod'
import { h } from 'vue'
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage
} from '@/components/ui/form'
import { ref } from 'vue'
import axios from 'axios'

// Переход на страницу регистрации

const router = useRouter()

const goToRegister = () => {
  router.push('/registerPage')
}

// Валидация полей ввода

const formSchema = toTypedSchema(
  z.object({
    login: z
      .string({ required_error: 'Поле не должно быть пустым' })

      .min(1, { message: 'Строка не должна быть пустой' })
      .max(20, { message: 'Логин должен содержать не больше 20 символов' })
      .regex(/^[a-z0-9_-]{3,20}$/, { message: 'Некоректный логин' }),
    password: z
      .string({ required_error: 'Поле не должно быть пустым' })
      .min(3, { message: 'Пароль должен содержать минимум 3 символа' })
      .max(20, { message: 'Пароль должен содержать не больше 20 символов' })
  })
)

const { handleSubmit, errors } = useForm({
  validationSchema: formSchema
})

const goToMain = () => {
  localStorage.clear()
  router.push('/')
}

const onSubmit = handleSubmit(async (formData) => {
  const apiFormData = new FormData()
  const userData = formData

  apiFormData.append('login', userData.login)
  apiFormData.append('password', userData.password)

  try {
    const response = await axios.post('http://localhost/auth-user', apiFormData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    console.log(response.data)

    if (response.data.status == 'success') {
      localStorage.clear()
      localStorage.setItem('id_user', response.data.id)
      localStorage.setItem('login', response.data.login)
      localStorage.setItem('full_name', response.data.full_name)
      router.push('/')
    }

    // Высплывашка тостер

    const { toast } = useToast()

    if (response.data.status == 'error') {
      toast({
        description: 'Ошибка авторизации, введен не правильный логин или пароль',
        variant: 'destructive'
      })
    }
  } catch (error) {
    console.error('Ошибка при отправке данных:', error)
  }
})

// Сделать логику для авторизации
</script>

<style scoped>
.bg-image {
  background-image: url('../img/hd-medical-health-icon-with-stethoscope-zwpo6s3pg7xqndlk.jpg');
  background-size: cover; /* Масштабировать изображение, чтобы оно покрывало весь элемент */
  background-position: center; /* Центрировать изображение */
  background-repeat: no-repeat; /* Не повторять изображение */
}
</style>
