<script setup lang="ts">
import RegisterForm from '@/components/RegisterForm.vue';
import type { Roles } from '@/enums/roles';
import { useRoute } from 'vue-router';

const route = useRoute();
const forType = route.meta.forType as Roles;
</script>
    
<template>
  <RegisterForm :for="forType" />
</template>


<style scoped>

</style>