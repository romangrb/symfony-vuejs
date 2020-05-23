<template>
  <div class="card shadow">
    <div class="card-header bg-transparent">
      <h3 class="mb-0">Edit Location</h3>
    </div>
    <div class="card-body">
      <loading :active.sync="is_processing"
               :loader="loader"
               :can-cancel="true"
               :opacity="0.8"
               :is-full-page="is_full_page">
      </loading>

      <form role="form">
        <base-input class="mb-3"
                    placeholder="Name"
                    v-model="model.name"
                    label="Name"
                    v-bind:error="errors.name">
        </base-input>

        <div class="form-group mb-3">
          <label class="form-control-label">Description</label>
          <textarea class="form-control mb-3"
                    rows="3"
                    placeholder="Description"
                    label="Description"
                    v-model="model.description"></textarea>
        </div>

        <div>
          <gmap-map
                  :center="center"
                  :zoom="12"
                  style="width:100%;  height: 400px;"
                  @click="onMapClick"
          >
            <gmap-marker
                    :key="index"
                    v-for="(m, index) in markers"
                    :position="m.position"
                    @click="center=m.position"
            ></gmap-marker>
          </gmap-map>
        </div>

        <div class="d-flex justify-content-between">
          <base-button type="primary" @click="cancel" class="my-4">Cancel</base-button>
          <base-button type="success" @click="save" class="my-4">Save</base-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import http from '../../services/httpClient';
  import loading from 'vue-loading-overlay';
  import 'vue-loading-overlay/dist/vue-loading.css';

  export default {
    name: "GoogleMap",
    components: {
      loading
    },
    data() {
      return {
        center: {lat: 45.508, lng: -73.587},
        markers: [],
        places: [],
        currentPlace: null,
        is_processing: true,
        is_full_page: false,
        loader:'Dots',
        model: {
          name: '',
          description: ''
        },
        errors: {
          name: '',
          description: ''
        }
      }
    },
    mounted() {
      http.get('place/' + this.$route.params.id).then((data) => {
        this.model.name = data.name;
        this.model.description = data.description;
        this.center.lat = 45.508;
        this.center.lng = -73.587;
        // this.center.lat = parseFloat(data.lat);
        // this.center.lng = parseFloat(data.lng);
        this.is_processing = false;
        console.log(data.lat);
      }),
      this.geolocate();
    },
    methods: {
      onMapClick(e) {
        this.markers.push({
          id: 1 + Math.max(0, ...this.markers.map(n => n.id)),
          position: e.latLng,
        });
      },
      onMarkerClick(e) {
        this.$refs.map.panTo(e.latLng);
      },
      geolocate: function () {
        navigator.geolocation.getCurrentPosition(position => {
          this.center = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
        });
      },
      save: function() {
        this.is_processing = true;

        let _this = this;

        http.patch('place/' + this.$route.params.id, this.model).then((data) => {
          this.is_processing = false;
          this.$router.push({name: 'locations'});
        }).catch(function (err) {
          Object.keys(_this.errors).forEach(function(key) {
            _this.errors[key] = err.response.data[key] || '';
          });
          _this.is_processing = false;
        });
      },
      cancel: function() {
        this.$router.push({ name: 'locations' });
      }
    }
  };
</script>

<style></style>
