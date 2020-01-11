<template>
  <div class="container">
      <div class="row">

        <div class="col-md-12">
          <!-- Start Table -->
          <table class="w-100">
            <thead>
              <tr>
                <th>#</th>
                <th>Id</th>
                <th>Name</th>
              </tr>
            </thead>
            <tbody v-if="events.length === 0">
              <tr class="text-center">
                <td colspan="6" class="text-center">
                  No Events
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr v-for="(event, index) in events" :key="event.id">
                <td> {{ index+1 }} </td>
                <td> {{ event.id }} </td>
                <td> {{ event.name }} </td>
              </tr>
            </tbody>
          </table>
          <!-- End Table -->
        </div>

      </div>
      <div class="row">
          <template>
              <paginate
                  :click-handler="pageChange"
                  :page-count=pages
                  :page-range="3"
                  :margin-pages="2"
                  v-model="page"
                  :prev-text="'Prev'"
                  :next-text="'Next'"
                  :container-class="'pagination'"
                  :page-class="'page-item'">
              </paginate>
          </template>
      </div>
  </div>
</template>

<script>
  export default {
    name: "Events",
    data() {
      return {
        events: {},
        page: 1,
        pages: 6,
      }
    },
    created() {
      this.loadEvents();
    },
    methods: {
      pageChange(page = 1){
          this.loadEvents(page);
      },
      loadEvents(page = 1){
        this.$http.get(`api/event?page=${page}`)
          .then(({data}) => {
            this.events = data.items;
            this.pages = 6;
            this.page = this.links.current;
          })
          .catch((e) => {
            console.warn(`Exception: ${e}`);
          })
          .finally(() => {
            this.busy = false
          })
      },
      clickCallback(pageNum){
        console.log(pageNum)
      }
    }
  }
</script>