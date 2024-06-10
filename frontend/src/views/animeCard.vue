<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <h1 class="">dasdas</h1>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import { Input } from '@/components/ui/input'
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Toaster } from '@/components/ui/toast'
import { useToast } from '@/components/ui/toast/use-toast'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Clapperboard } from 'lucide-vue-next'
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev
} from '@/components/ui/pagination'
import { CircleX } from 'lucide-vue-next'
import { Cog } from 'lucide-vue-next'

const items = ref([])

const avatar = ref('')

const animeFetch = async () => {
  try {
    const response = await axios.get('http://localhost/anime')
    items.value = response.data

    console.log(items.value)
  } catch (err) {
    console.error(err)
  }
}

onMounted(animeFetch)
</script>

<style lang="scss" scoped></style>
