<template>
    <!-- prettier-ignore -->
    <app-layout>
        <edit-item ref="dlg2" :accounting_item="currentItem" />
        <confirm ref="dlg4" @confirmed="toggleItemStatus2()">
			<jet-label for="type"  :value="__('Are you sure you want to delete this item?')" />
		</confirm>
		<div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <button class="btn btn-primary" @click="newItemMap()">
                    {{ __("New") }}
                </button>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
					<Table
						:filters="queryBuilderProps.filters"
						:search="queryBuilderProps.search"
						:columns="queryBuilderProps.columns"
						:on-update="setQueryBuilder"
						:meta="items"
				  	>
						<template #head>
						  	<tr>
                                  <template v-for="(col, key) in queryBuilderProps.columns" :key="key">
                                      <th v-show="show(key)" 
                                      v-if="notSortableCols.includes(key)">{{ col.label }}</th>
                                      <th class="cursor-pointer" v-show="show(key)" @click.prevent="sortBy(key)" v-else>{{ col.label }}</th>
                                  </template>
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id" 
                                :class="{ credit: item.documentType =='C', debit: item.documentType =='D' }"
                            >
									<td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="show(key)">
										<div v-for="rowVals in nestedIndex(item, key).split(',')">
											{{ rowVals }}
                                        </div>
									</td>
									<td>
                                        <div class="grid grid-cols-3 w-56">
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="toggleItemStatus(item)" 
                                            >
                                                {{ item.status == "Active" ?  __("Deactivate") : __("Activate") }}
                                            </jet-danger-button>
                                            
                                            <jet-button
                                                class="me-2 mt-2"
                                                @click="editItem(item)" 
                                            >
                                                {{ __("Edit") }}
                                            </jet-button>
                                        </div>
									</td>
							  </tr>
						</template>
				 	</Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    Table
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Confirm from "@/UI/Confirm.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue';
import Dropdown from "@/Jetstream/Dropdown.vue";
import EditItem from "@/Pages/Accounting/Chart/Edit.vue";

export default {
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        JetLabel,
        Table,
        JetButton,
        JetDangerButton,
        SecondaryButton,
        EditItem,
    },
    props: {
        items: Object,
    },
    data() {
        return {
            currentItem: { },
            notSortableCols: [
            ],
        };
    },
    methods: {
        editItem(item) {
            this.currentItem = item;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
        },        
        toggleItemStatus(item) {
            this.currentItem = item;
            axios
                .post(route("accounting.chart.delete"), {
                    id: this.currentItem.id,
                    status: this.currentItem.status == "Active" ? "Inactive" : "Active",
                })
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.currentItem.status = this.currentItem.status == "Active" ? "Inactive" : "Active";
                })
                .catch((error) => {
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
        },
        nestedIndex: function (item, key) {
            try {
                var keys = key.split(".");
                if (keys.length == 1) return item[key].toString();
                if (keys.length == 2) return item[keys[0]][keys[1]].toString();
                if (keys.length == 3)
                    return item[keys[0]][keys[1]][keys[2]].toString();
                return "Unsupported Nested Index";
            } catch (err) {}
            return "N/A";
        },
    },
};
</script>
<style scoped>
:deep(table td) {
    text-align: start;
	white-space: pre-line;
}
:deep(table th) {
    text-align: start;
	white-space: pre-line;
}
.credit {
    background-color: lightgoldenrodyellow;
}
.debit {
    background-color: palegreen;
}
</style>
