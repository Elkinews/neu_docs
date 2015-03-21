<template>
    <nde-table>
        <template v-slot:ndeTableContent>
            <thead>
                <tr>
                    <th>From Tabs</th>
                    <th>Type</th>
                    <th>To Tabs</th>
                    <th>To Type</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.sourceId">
                    <td>{{ item.fromTab }}</td>
                    <td>{{ item.type }}</td>
                    <td>
                        <v-autocomplete
                            v-model="item.isTransfer"
                            :items="toTabTransferOptions"
                            outlined
                        ></v-autocomplete>
                    </td>
                    <td>
                        <v-autocomplete
							v-show="item.isTransfer"
                            v-model="item.tabId"
                            :items="item.toTabOptions"
							item-text="name"
							item-value="id"
                            outlined
                        ></v-autocomplete>
                    </td>
                </tr>
            </tbody>
        </template>
    </nde-table>
</template>

<script>
import NdeTable from "./NdeTable.vue";

export default {
    name: "NdeTableTransferTabs",
    components: {
        NdeTable,
    },
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        // TODO: Will delete when there is data get from API
        return {
            toTabTransferOptions: [
				{
					text: 'Transfer',
					value: true,
				},
				{
					text: 'Do Not Transfer',
					value: false,
				}
            ],
        };
    },
};
</script>

<style scoped lang="scss">
</style>
