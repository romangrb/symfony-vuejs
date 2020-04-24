
<template>

  <div class="card shadow" :class="type === 'dark' ? 'bg-default': ''">
    <div class="card-header border-0"
         :class="type === 'dark' ? 'bg-transparent': ''">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0" :class="type === 'dark' ? 'text-white': ''">
            {{title}}
          </h3>
        </div>
      </div>
    </div>

    <div class="table-responsive">

      <loading :active.sync="is_processing"
               :loader="loader"
               :can-cancel="true"
               :on-cancel="onCancel"
               :opacity="0.8"
               :is-full-page="is_full_page">
      </loading>

      <base-table class="table align-items-center table-flush"
                  :class="type === 'dark' ? 'table-dark': ''"
                  :thead-classes="type === 'dark' ? 'thead-dark': 'thead-light'"
                  tbody-classes="list"
                  :data="tableData">

        <template slot="columns">
          <th @click="orderBy('id')">
            <tr><fa :prefix="iconPrefix" :icon="orderClass.id" /> # </tr>
          </th>
          <th @click="orderBy('name')">
            <tr><fa :prefix="iconPrefix" :icon="orderClass.name" /> Name </tr>
          </th>
          <th>Description</th>
          <th @click="orderBy('updated_at')">
            <tr><fa :prefix="iconPrefix" :icon="orderClass.updated_at" /> Last Update </tr>
          </th>
        </template>

        <template slot-scope="{row}">
          <td>
            {{row.id}}
          </td>
          <th scope="row">
            {{row.name}}
          </th>
          <td style="width: 90px;">
            {{row.description}}
          </td>
          <td>
            {{row.updated_at | formatDate}}
          </td>
        </template>
      </base-table>
    </div>

    <div class="card-footer d-flex justify-content-end" :class="type === 'dark' ? 'bg-transparent': ''">
      <base-pagination
              :total="pagination.total"
              :per-page="pagination.per_page"
              v-model="pagination.page"
      >
      </base-pagination>
    </div>
  </div>
</template>

<script>
  // Import component
  import Loading from 'vue-loading-overlay';
  // Import stylesheet
  import 'vue-loading-overlay/dist/vue-loading.css';

  import moment from 'moment';

  const fa_order = 'sort-alpha-';

  export default {
    name: 'locations-table',
    components: {
      Loading
    },
    props: {
      type: {
        type: String
      },
      title: String
    },
    data() {
      return {
        tableData: [],
        is_processing: true,
        is_full_page: false,
        loader:'Dots',
        pagination: {
          total: '0',
          per_page: 0,
          page: 1
        },
        order_by: 0,
        order_type: 'id',
        orderClass: {
          'id':'sort-alpha-down',
          'name':'sort-alpha-down',
          'updated_at':'sort-alpha-down'
        }
      }
    },
    computed: {
      iconPrefix() {
        return 'fas';
      },
    },
    filters: {
      formatDate: function (value) {
        if (! value) return '';
        return moment(String(value)).format('DD/MM/YY hh:mm');
      }
    },
    watch: {
      "pagination.page" : function(page) {
        this.pagination.page = page;
        this.loadPlaces();
      }
    },
    created() {
      this.loadPlaces();
    },
    methods: {
      orderBy(type) {

        if (this.order_type === type) {
          this.order_by = this.order_by ? 0 : 1;
        } else {
          this.order_type = type;
          this.order_by = 0;
        }
        this.resetOrderByIcons();
        this.orderClass[type] = fa_order + (this.order_by ? 'up' : 'down');

        this.loadPlaces();
      },
      onCancel() {
        this.is_processing = false;
        this.$router.push('Dashboard');
      },
      resetOrderByIcons() {
        let fa_class = fa_order + 'down';

        for (let key in this.orderClass){
          this.orderClass[key] = fa_class;
        }
      },
      loadPlaces(){

        this.is_processing = true;

        this.$http.get(`api/v1/places?page=${this.pagination.page}&order_by=${this.order_by}&order_type=${this.order_type}`)
          .then(({data}) => {
            this.tableData = data.items;
            this.pagination.total = data.total.toString();
            this.pagination.per_page =  data.per_page;
            this.pagination.page = data.links.current;
          })
          .catch((e) => {
            console.warn(`Exception: ${e}`);
          })
          .finally(() => {
            this.is_processing = false;
          })
      }
    },
  }
</script>
<style>
</style>
