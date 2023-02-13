<script setup lang="ts">
    import { Roles } from '@/enums/roles';
    import { axios } from '@/services/auth';
    import { ref, computed } from 'vue';
    import router from '@/router';

    const users = ref();
    const selection = ref([]);

    try {
        const response = await axios.get('https://localhost/users/', { params: {roles: Roles.Tenant}});
        users.value = response.data;
        console.log(response);
    } catch (error) {
        console.log(error);
    }

    const showedUsers = computed(() => {
        if (!selection.value.length) {
            return users.value;
        }
        let showed: any[] = [];
        selection.value.forEach((status: string) => {
            showed = showed.concat(users.value.filter((user: any) => user.validationStatus === status));
        });
        return showed;
    });
</script>

<template>
    <div class="mx-16 h-75">
        <h1 class="mb-10 text-h4 font-weight-bold">Dossiers locataires</h1>
        <v-chip-group multiple selected-class="text-primary" v-model="selection">
            <v-chip filter class="ma-2" size="x-large" value="to_complete">A compléter</v-chip>
            <v-chip filter class="ma-2" size="x-large" value="to_review">A valider</v-chip>
            <v-chip filter class="ma-2" size="x-large" value="validated">Validés</v-chip>
        </v-chip-group>
        <v-table>
            <thead>
                <tr class="">
                    <th class="text-left font-weight-bold">Prénom</th>
                    <th class="text-left">Nom</th>
                    <th class="text-left">Situation</th>
                    <th class="text-left">Revenus</th>
                    <th class="text-left">Statut du dossier</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in showedUsers" :key="user.email" @click="router.push({ name: 'agency_show_user', params: {id: user.id}})">
                    <td>{{ user.firstname }}</td>
                    <td>{{ user.lastname }}</td>
                    <td>{{ $t(`work_situation.${user.situation}`) }}</td>
                    <td>{{ user.salary ?? '0' }} €</td>
                    <td>{{ $t(`validation_status.${user.validationStatus}`) }}</td>
                </tr>
            </tbody>
        </v-table>
    </div>
</template>