<template>
  <div class="flex items-center justify-center h-screen">
    <div class="flex flex-col items-center gap-10">
      <h1 class="font-bold">Зарегистрируйтесь</h1>
      <div class="border-t h-5 w-96 border-purple-600"></div>

      <div class="grid w-full max-w-sm items-center gap-1.5">
        <form class="w-96 space-y-6" @submit="onSubmit">
          <FormField v-slot="{ componentField: loginField }" name="login">
            <FormItem>
              <FormLabel>Логин</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Введите ваш логин" v-bind="loginField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField: FioField }" name="FIO">
            <FormItem>
              <FormLabel>ФИО</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Введите ваше ФИО" v-bind="FioField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField: passwordField }" name="password">
            <FormItem>
              <FormLabel>Пароль</FormLabel>
              <FormControl>
                <Input type="password" placeholder="Придумайте пароль" v-bind="passwordField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Чекбокс -->
          <FormField v-slot="{ value, handleChange }" type="checkbox" name="person">
            <FormItem class="flex flex-row items-start gap-x-3 space-y-0 rounded-md border p-4">
              <FormControl>
                <Checkbox :checked="value" @update:checked="handleChange" />
              </FormControl>
              <div class="space-y-1 leading-none">
                <FormLabel>Согласен на обработку персональных данных</FormLabel>
                <FormMessage />
              </div>
            </FormItem>
          </FormField>

          <div class="flex">
            <Toaster />
            <Button type="submit" class="bg-purple-500 w-64 text-white mr-5">
              Зарегистрироваться
            </Button>
            <Button @click="gotToLogin" class="w-32 bg-slate-400"> Войти </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Toaster } from '@/components/ui/toast'
import { useToast } from '@/components/ui/toast/use-toast'
import { useRoute, useRouter } from 'vue-router'
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import * as z from 'zod'
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
import { Checkbox } from '@/components/ui/checkbox'

const router = useRouter()

const gotToLogin = () => {
  router.push('/auth')
}

// Сделать логику для регистрации

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
      .max(20, { message: 'Пароль должен содержать не больше 20 символов' }),
    FIO: z
      .string({ required_error: 'Поле не должно быть пустым' })
      .regex(/([А-ЯЁ][а-яё]+[\-\s]?){3,}/, { message: 'Заполните правильно ФИО' }),
    person: z.boolean().default(false).optional()
  })
)

const { handleSubmit, errors } = useForm({
  validationSchema: formSchema,
  initialValues: {
    person: true
  }
})

const onSubmit = handleSubmit(async (formData) => {
  const apiFormData = new FormData()
  const userData = formData

  console.log(userData)

  console.log('Клик')

  apiFormData.append('login', userData.login)
  apiFormData.append('fio', userData.FIO)
  apiFormData.append('password', userData.password)

  try {
    const response = await axios.post('http://localhost/add-user', apiFormData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    console.log(response.data)

    // Всплывашка тостер

    const { toast } = useToast()

    if (response.data.status == 'error') {
      toast({
        description: 'Ошибка регистрации, пользователь с таким логином уже существует',
        variant: 'destructive'
      })
      return
    }

    if (response.data.status == 'success') {
      localStorage.clear()
      localStorage.setItem('id_user', response.data.User.id)
      localStorage.setItem('login', response.data.User.login)
      localStorage.setItem('full_name', response.data.User.fio)
      router.push('/')
    }
  } catch (error) {
    console.error('Ошибка при отправке данных:', error)
  }
})
</script>

<style lang="scss" scoped></style>
