<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { axios } from '@/services/auth';
    import { ref } from 'vue';
    import { Roles } from '@/enums/roles';
    import { RequestEnum } from '@/enums/RequestEnum';

    const viewing = ref();
    const agents = ref();
    const selectedAgent = ref();
    const route = useRoute();
    const loading = ref(false);
    const form = ref();
    const valid = ref();

    try {
        const response = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/viewings/${route.params.id}`);
        selectedAgent.value = response.data.agent;
        viewing.value = response.data;
        const agentsResponse = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/users`, { params: {roles: Roles.Agency}});
        agents.value = agentsResponse.data;
    } catch (error) {
        console.log(error);
    }

    const handleSubmit = async () => {
        loading.value = true;
        if (valid.value) {
            console.log(selectedAgent.value);
            try {
                const response = await axios.put(`${import.meta.env.VITE_BASE_API_URL}/viewings/${viewing.value.id}`, {
                    agent: `/users/${selectedAgent.value}`,
                });
                const response2 = await axios.put(`${import.meta.env.VITE_BASE_API_URL}/requests/${response.data.availaibility.request.id}`, {
                    state: RequestEnum.Visit,
                });
                console.log(response);
                viewing.value.agent = response.data.agent;
            } catch (error) {
                console.log(error);
            }
        } else {
            form.value.validate();
        }
        loading.value = false;
        selectedAgent.value = viewing.value.agent;
    }
</script>
<template>
    <div class="mx-16 h-75">
        <h1 class="mb-10 text-h4 font-weight-bold">Visite #{{ viewing.id }}</h1>
        <v-row>
            <v-col>
            <v-card>
                <v-list>
                    <v-list-item>
                        <v-list-item-title class="text-h7 font-weight-bold">Locataire</v-list-item-title>
                        {{ viewing.lodger.firstname }} {{ viewing.lodger.lastname }}
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title class="text-h7 font-weight-bold">Agent</v-list-item-title>
                        {{ viewing.agent?.firstname }} {{ viewing.agent?.lastname }}
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title class="text-h7 font-weight-bold">Propriétaire</v-list-item-title>
                        {{ viewing.availaibility.request.property.owner.firstname }} {{ viewing.availaibility.request.property.owner.lastname }}
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title class="text-h7 font-weight-bold">Bien</v-list-item-title>
                        <router-link :to="{ name: `${Roles.Agency}_property_details`, params: {id: viewing.availaibility.request.property.id} }" class="text-decoration-underline text-indigo">
                            {{ viewing.availaibility.request.property.title }}
                        </router-link>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title class="text-h7 font-weight-bold">Créneau</v-list-item-title>
                        {{ viewing.availaibility.slot }}
                    </v-list-item>
                </v-list>
            </v-card>
        </v-col>
        <v-col>

            <v-card>
                <v-card-title>Assigner un agent</v-card-title>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-select
                            :items="agents"
                            label="Agent"
                            v-model="selectedAgent"
                            :item-title="item => item.firstname"
                            item-value="id"
                            outlined
                            dense
                            required
                            :rules="[v => !!v || 'Veuillez sélectionner un agent']"
                        ></v-select>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-progress-circular v-if="loading" indeterminate color="primary" class="mt-5"></v-progress-circular>
                    <v-btn v-else color="primary" variant="tonal" @click="handleSubmit()">Assigner</v-btn>
                </v-card-actions>
            </v-card>
            </v-col>
        </v-row>
    </div>
</template>