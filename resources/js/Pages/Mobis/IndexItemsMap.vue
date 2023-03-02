<template>
    <!-- prettier-ignore -->
    <app-layout>
        <edit-item-map ref="dlg2" :item_map="currentItem" />
        <confirm ref="dlg4" @confirmed="deleteItemMap2()">
			<jet-label for="type"  :value="__('Are you sure you want to delete this item?')" />
		</confirm>
		<div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
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
                                      <th v-show="showColumn(key)" 
                                      v-if="notSortableCols.includes(key)">{{ col.label }}</th>
                                      <th class="cursor-pointer" v-show="showColumn(key)" @click.prevent="sortBy(key)" v-else>{{ col.label }}</th>
                                  </template>
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id" 
                                :class="{ credit: item.documentType =='C', debit: item.documentType =='D' }"
                            >
									<td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
										<div v-for="rowVals in nestedIndex(item, key).split(',')">
											{{ rowVals }}
                                        </div>
									</td>
									<td>
                                        <div class="grid grid-cols-3 w-56">
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="deleteItemMap(item)" 
                                                v-show="item.status!='Valid' && item.status!='approved'" 
                                                >
                                                {{ __("Delete") }}
                                            </jet-danger-button>
                                            
                                            <jet-button
                                            class="me-2 mt-2"
                                                @click="editItemMap(item)" 
                                                v-show="item.status!='Valid'"
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
import AppLayout from "@/Layouts/AppLayout";
import {
    InteractsWithQueryBuilder,
    Tailwind2,
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Confirm from "@/UI/Confirm";
import JetLabel from "@/Jetstream/Label";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton';
import Dropdown from "@/Jetstream/Dropdown";
import EditItemMap from "@/Pages/Mobis/EditItemMap";
import swal from "sweetalert";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        JetLabel,
        Table: Tailwind2.Table,
        JetButton,
        JetDangerButton,
        SecondaryButton,
        EditItemMap,
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
        editItemMap(item) {
            this.currentItem = item;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
        },        
        deleteItemMap(item) {
            this.currentItem = item;
            this.$refs.dlg4.ShowModal();
        },
        deleteItemMap2() {
            axios
                .post(route("ms.items.map.delete"), {MSCode: this.currentItem.MSCode})
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.showDialog = false;
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
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
        editItem: function (item_id) {
            //alert(JSON.stringify(item_id));
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
