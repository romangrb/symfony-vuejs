
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
          <th>#</th>
          <th>Name</th>
          <th>Description</th>
          <th>Last Update</th>
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
            {{row.updated_at.date}}
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
        }
      }
    },
    watch: {
      "pagination.page" : function(next_number) {
        this.loadPlaces(next_number);
      }
    },
    created() {
      this.loadPlaces();
    },
    methods: {
      onCancel() {
        this.is_processing = false;
        this.$router.push('Dashboard');
      },
      loadPlaces(page = 1){

        this.is_processing = true;

        this.$http.get(`api/v1/places?page=${page}`)
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
