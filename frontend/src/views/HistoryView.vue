<template>
  <section class="flex flex-col gap-6 w-full pb-16">
    <div class="flex flex-col gap-2">
      <h3 class="font-bold">Просмотренные аниме</h3>
    </div>
    <div class="flex flex-col">
      <Table class="w-full">
        <TableHeader>
          <TableRow>
            <TableHead class="font-bold">Название аниме</TableHead>
            <TableHead class="font-bold">Год релиза</TableHead>
            <TableHead class="font-bold">Дата просмотра</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody v-for="item in historyData" :key="item[0]">
          <TableRow>
            <TableCell>{{ item[1] }}</TableCell>
            <TableCell>{{ item[2] }}</TableCell>
            <TableCell>{{ item[3] }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </section>
</template>

<script setup>
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'

import { ref } from 'vue'

const historyData = ref([
  [1, 'Наруто', '2002', '2023-06-01'],
  [2, 'Тетрадь смерти', '2006', '2023-06-01'],
  [3, 'Атака титанов', '2013', '2023-06-01']
])
</script>
