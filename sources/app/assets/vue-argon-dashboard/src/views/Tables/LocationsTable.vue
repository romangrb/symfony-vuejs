
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
            {{1}}
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

    <div class="card-footer d-flex justify-content-end"
         :class="type === 'dark' ? 'bg-transparent': ''">
      <base-pagination total="30"></base-pagination>
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
      }
    },
    created() {
      this.loadPlaces();
    },
    methods: {
      pageChange(page = 1){
        this.loadPlaces(page);
      },
      onCancel() {
        this.is_processing = false;
        this.$router.push('Dashboard');
      },
      loadPlaces(page = 1){

        this.is_processing = true;

        this.$http.get(`api/v1/places?page=${page}`)
          .then(({data}) => {
            this.tableData = data.items;
            this.pages = data.total;
            this.page = this.links.current;
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
