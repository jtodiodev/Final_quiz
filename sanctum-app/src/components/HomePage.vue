<template>
  <div>
    <nav class="navbar navbar-expand-lg custom-bg">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li>
                  <router-link to="/logout" class="dropdown-item">Logout</router-link>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- the router view is where all the routes are rendered -->
    <div class="container mt-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Create a Post</h5>
          <div class="mb-3">
            <textarea class="form-control" id="postContent" rows="3" placeholder="What's on your mind?"></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary">Post</button>
          </div>
        </div>
      </div>
    </div>
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'HomePage'
}
</script>

<style>
.custom-bg {
  background-color: #1d1927; /* Replace with your desired color */
}

.text-white {
  color: white !important;
}

.card {
  margin-top: 20px;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}
</style>
