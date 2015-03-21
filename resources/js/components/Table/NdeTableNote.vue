<template>
    <nde-table>
        <template v-slot:ndeTableContent>
            <thead>
                <tr>
                    <th>Created</th>
                    <th>Author</th>
                    <th>Note</th>
                    <th>Subject</th>
                    <th>Location</th>
                    <th>Extra Field</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>{{ item.created }}</td>
                    <td>{{ item.author }}</td>
                    <td>{{ item.note }}</td>
                    <td>{{ item.subject }}</td>
                    <td>{{ item.location }}</td>
                    <td>{{ item.extraField }}</td>
                    <td>
                        <nde-button
                            fab
                            style="width: 1.5rem; height: 1.5rem"
                            color="#F9E1E1"
                        >
                            <v-icon size="18" color="#C32C39">
                                mdi-close
                            </v-icon>
                        </nde-button>
                    </td>
                </tr>
            </tbody>
        </template>
    </nde-table>
</template>

<script>
import NdeTable from "./NdeTable.vue";
import NdeButton from "../Button/NdeButton.vue";

export default {
    name: "NdeTableNote",
    components: {
        NdeTable,
        NdeButton,
    },
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
};
</script>

<style scoped lang="scss">
</style>
