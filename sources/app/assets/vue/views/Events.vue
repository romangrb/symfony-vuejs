<template>
  <div class="row">
    <div class="col-md-12">
      <!-- Start Table -->
      <table class="w-100 my-3">
        <thead>
          <tr>
            <th>Actions</th>
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
            <td>
              {{ event }}
            </td>
          </tr>
        </tbody>
      </table>
      <!-- End Table -->
    </div>
  </div>
</template>

<script>
  export default {
    name: "Events",
    data() {
      return {
        events: [],
      }
    },
    created() {
      this.loadEvents();
    },
    methods: {
      loadEvents() {
        this.$http.get('api/event')
          .then(({data}) => {
            this.events = data
          })
          .catch(() => {
            this.$swal('Error', 'Something went wrong...', 'error');
          })
          .finally(() => {
            this.busy = false
          })
      },
    }
  }
</script>