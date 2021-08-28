<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
								<th v-show="showColumn('codeTypeName')" @click.prevent="sortBy('codeTypeName')">'codeTypeName'</th>
								<th v-show="showColumn('codeID')" @click.prevent="sortBy('codeID')">'codeID'</th>
								<th v-show="showColumn('itemCode')" @click.prevent="sortBy('itemCode')">'itemCode'</th>
								<th v-show="showColumn('codeNamePrimaryLang')" @click.prevent="sortBy('codeNamePrimaryLang')">'codeNamePrimaryLang'</th>
								<th v-show="showColumn('codeNameSecondaryLang')" @click.prevent="sortBy('codeNameSecondaryLang')">'codeNameSecondaryLang'</th>
								<th v-show="showColumn('descriptionPrimaryLang')" @click.prevent="sortBy('descriptionPrimaryLang')">'descriptionPrimaryLang'</th>
								<th v-show="showColumn('descriptionSecondaryLang')" @click.prevent="sortBy('descriptionSecondaryLang')">'descriptionSecondaryLang'</th>
								<th v-show="showColumn('parentCodeID')" @click.prevent="sortBy('parentCodeID')">'parentCodeID'</th>
								<th v-show="showColumn('parentItemCode')" @click.prevent="sortBy('parentItemCode')">'parentItemCode'</th>
								<th v-show="showColumn('parentCodeNamePrimaryLang')" @click.prevent="sortBy('parentCodeNamePrimaryLang')">'parentCodeNamePrimaryLang'</th>
								<th v-show="showColumn('parentCodeNameSecondaryLang')" @click.prevent="sortBy('parentCodeNameSecondaryLang')">'parentCodeNameSecondaryLang'</th>
								<th v-show="showColumn('parentLevelName')" @click.prevent="sortBy('parentLevelName')">'parentLevelName'</th>
								<th v-show="showColumn('levelName')" @click.prevent="sortBy('levelName')">'levelName'</th>
								<th v-show="showColumn('requestCreationDateTimeUtc')" @click.prevent="sortBy('requestCreationDateTimeUtc')">'requestCreationDateTimeUtc'</th>
								<th v-show="showColumn('codeCreationDateTimeUtc')" @click.prevent="sortBy('codeCreationDateTimeUtc')">'codeCreationDateTimeUtc'</th>
								<th v-show="showColumn('activeFrom')" @click.prevent="sortBy('activeFrom')">'activeFrom'</th>
								<th v-show="showColumn('activeTo')" @click.prevent="sortBy('activeTo')">'activeTo'</th>
								<th v-show="showColumn('active')" @click.prevent="sortBy('active')">'active'</th>
								<th v-show="showColumn('status')" @click.prevent="sortBy('status')">'status'</th>
								<th v-show="showColumn('statusReason')" @click.prevent="sortBy('statusReason')">'statusReason'</th>
								<th @click.prevent="">Actions</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id">
								 <td v-show="showColumn('codeTypeName')">{{ item.codeTypeName }}</td>
								 <td v-show="showColumn('codeID')">{{ item.codeID }}</td>
								 <td v-show="showColumn('itemCode')">{{ item.itemCode }}</td>
								 <td v-show="showColumn('codeNamePrimaryLang')">{{ item.codeNamePrimaryLang }}</td>
								 <td v-show="showColumn('codeNameSecondaryLang')">{{ item.codeNameSecondaryLang }}</td>
								 <td v-show="showColumn('descriptionPrimaryLang')">{{ item.descriptionPrimaryLang }}</td>
								 <td v-show="showColumn('descriptionSecondaryLang')">{{ item.descriptionSecondaryLang }}</td>
								 <td v-show="showColumn('parentCodeID')">{{ item.parentCodeID }}</td>
								 <td v-show="showColumn('parentItemCode')">{{ item.parentItemCode }}</td>
								 <td v-show="showColumn('parentCodeNamePrimaryLang')">{{ item.parentCodeNamePrimaryLang }}</td>
								 <td v-show="showColumn('parentCodeNameSecondaryLang')">{{ item.parentCodeNameSecondaryLang }}</td>
								 <td v-show="showColumn('parentLevelName')">{{ item.parentLevelName }}</td>
								 <td v-show="showColumn('levelName')">{{ item.levelName }}</td>
								 <td v-show="showColumn('requestCreationDateTimeUtc')">{{ item.requestCreationDateTimeUtc }}</td>
								 <td v-show="showColumn('codeCreationDateTimeUtc')">{{ item.codeCreationDateTimeUtc }}</td>
								 <td v-show="showColumn('activeFrom')">{{ item.activeFrom }}</td>
								 <td v-show="showColumn('activeTo')">{{ item.activeTo }}</td>
								 <td v-show="showColumn('active')">{{ item.active }}</td>
								 <td v-show="showColumn('status')">{{ item.status }}</td>
								 <td v-show="showColumn('statusReason')">{{ item.statusReason }}</td>

									<td>
										<div class="grid justify-items-center">
                    						<add-edit-item :item="item" @confirmed="enableTwoFactorAuthentication">
		                        				<jet-button type="button" >
												<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#FFFFFF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z"/></svg>
						                        </jet-button>
						                    </add-edit-item>
<!--											<jet-button @click.prevent="editItem(item)">
											</jet-button> -->
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
    import AppLayout from '@/Layouts/AppLayout'
	import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';
    import JetButton from '@/Jetstream/Button';
	import AddEditItem from '@/Pages/Items/AddEdit';

    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout,
			Table: Tailwind2.Table,
			JetButton,
			AddEditItem,
        },
		props: {
			items: Object
  		},
		methods: {
			editItem: function(item_id) {
				//alert(JSON.stringify(item_id));
			}
		}
    }
</script>
