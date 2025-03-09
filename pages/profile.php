<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css"
        integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"
        integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../designs/styles.css">
    <title>Document</title>
    <style>
    /* Hide all sections by default */
    .content-section {
        display: none;
    }

    /* Show the active section */
    .content-section.active {
        display: block;
    }

    /* Disabled input styling */
    input:read-only {
        background-color: #f9f9f9;
        cursor: not-allowed;
    }
    </style>
</head>

<body>
    <div class="container-fluid mx-0">
        <div class="profile-layout">
            <div class="side-bar">
                <div class="user-profile">
                    <div class="avatar">
                        <img style="height: 80px; width: 80px; border-radius: 50%;"
                            src="https://api.dicebear.com/7.x/avataaars/svg?seed=john" alt="John Doe" />
                    </div>
                </div>
                <div class="info text-center">
                    <h3>John Doe</h3>
                    <small class="text-muted">JohnDoe@gmail.com</small>
                </div>
                <div class="edit d-flex justify-content-center">
                    <button id="edit-profile-btn" class="btn btn-outline-secondary w-75 mt-3">Edit Profile</button>
                </div>
                <hr>
                <div class="side-nav">
                    <small class="text-muted px-3">Navigations</small>
                    <ul>
                        <li><button class="nav-item active btn" data-section="personal-information"><span
                                    class="icon">👤</span>&nbsp;Personal Information</button></li>
                        <li><button class="nav-item btn" data-section="security-settings"><span
                                    class="icon">🔒</span>&nbsp;Security Settings</button></li>
                    </ul>
                </div>
                <hr>
                <div class="side-footer px-4">
                    <button class="btn btn-outline-danger form-control">Log out &nbsp;<i
                            class="bi bi-box-arrow-in-left"></i></button>
                </div>
            </div>
            <div class="main-content">
                <div class="contents">
                    <!-- Personal Information Section -->
                    <div class="content-section personal-information p-md-2 mt-4 active">
                        <div class="personal-info">
                            <h3>Personal Information</h3>
                            <div class="form-group">
                                <form action="">
                                    <div class="row">
                                        <div class="col-md-4 my-md-4">
                                            <label for="first-name">First Name</label>
                                            <input type="text" class="form-control" id="first-name"
                                                placeholder="First Name" readonly>
                                        </div>
                                        <div class="col-md-4 my-md-4">
                                            <label for="last-name">Last Name</label>
                                            <input type="text" class="form-control" id="last-name"
                                                placeholder="Last Name" readonly>
                                        </div>
                                        <div class="col-md-4 my-md-4">
                                            <label for="middle-name">Middle Name</label>
                                            <input type="text" class="form-control" id="middle-name"
                                                placeholder="Middle Name" readonly>
                                        </div>
                                        <div class="col-md-6 my-md-4">
                                            <label for="phone-number">Phone Number</label>
                                            <input type="text" class="form-control" id="phone-number"
                                                placeholder="Phone Number" readonly>
                                        </div>
                                        <div class="col-md-6 my-md-4">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" placeholder="Email"
                                                readonly>
                                        </div>
                                        <div class="col-md-6 my-md-4">
                                            <label for="age">Age</label>
                                            <input type="text" class="form-control" id="age" placeholder="Age" readonly>
                                        </div>
                                        <div class="col-md-6 my-md-4">
                                            <label for="gender">Gender</label>
                                            <input type="text" class="form-control" id="gender" placeholder="Gender"
                                                readonly>
                                        </div>
                                        <div class="col my-md-3 my-md-4">
                                            <label for="address">Street</label>
                                            <input type="text" class="form-control" id="address" placeholder="Street"
                                                readonly>
                                        </div>
                                        <div class="col-md-3 my-md-4">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" id="city" placeholder="City"
                                                readonly>
                                        </div>
                                        <div class="col-md-3 my-md-4">
                                            <label for="province">Province</label>
                                            <input type="text" class="form-control" id="province" placeholder="Province"
                                                readonly>
                                        </div>
                                        <div class="col-md-3 my-md-4">
                                            <label for="postal-code">Postal Code</label>
                                            <input type="text" class="form-control" id="postal-code"
                                                placeholder="Postal Code" readonly>
                                        </div>
                                        <div class="col-md-12 my-md-4 d-flex justify-content-center">
                                            <button type="submit" style="width: 16rem;" class="btn btn-primary">Save
                                                Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings Section -->
                    <div class="content-section security-settings p-md-2 mt-4">
                        <div class="security-info">
                            <h3>Security Settings</h3>
                            <div class="form-group">
                                <form action="">
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-md-4 my-md-4">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" placeholder=""
                                                readonly>
                                        </div>
                                        <div class="col-md-4 my-md-4">
                                            <label for="confirmusername">Confirm Username</label>
                                            <input type="text" class="form-control" id="confirmusername" placeholder=""
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row d-flex justify-content-center align-items-center">
                                        <div class="col-md-4 my-md-4">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" placeholder=""
                                                readonly>
                                        </div>
                                        <div class="col-md-4 my-md-4">
                                            <label for="confirmpassword">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirmpassword"
                                                placeholder="" readonly>
                                        </div>
                                        <div class="col-md-12 my-md-4 d-flex justify-content-center">
                                            <button type="submit" style="width: 16rem;" class="btn btn-primary">Save
                                                Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to handle section changes
        function handleSectionChange(sectionId) {
            // Hide all sections
            document.querySelectorAll(".content-section").forEach(section => {
                section.classList.remove("active");
            });

            // Show the selected section
            document.querySelector(`.${sectionId}`).classList.add("active");

            // Remove "active" from all buttons
            document.querySelectorAll(".nav-item").forEach(btn => {
                btn.classList.remove("active");
            });

            // Add "active" to the clicked button
            document.querySelector(`[data-section="${sectionId}"]`).classList.add("active");
        }

        // Add event listeners to navigation buttons
        document.querySelectorAll(".nav-item").forEach(item => {
            item.addEventListener("click", function() {
                const sectionId = this.getAttribute("data-section");
                handleSectionChange(sectionId);
            });
        });

        // Show the default section (Personal Information) on page load
        handleSectionChange("personal-information");

        // Toggle edit mode
        const editProfileBtn = document.getElementById("edit-profile-btn");
        const inputs = document.querySelectorAll("input");

        editProfileBtn.addEventListener("click", function() {
            inputs.forEach(input => {
                input.readOnly = !input.readOnly;
            });

            // Change button text
            if (editProfileBtn.textContent === "Edit Profile") {
                editProfileBtn.textContent = "Cancel Edit";
                editProfileBtn.classList.remove("btn-outline-secondary");
                editProfileBtn.classList.add("btn-outline-danger");
            } else {
                editProfileBtn.textContent = "Edit Profile";
                editProfileBtn.classList.remove("btn-outline-danger");
                editProfileBtn.classList.add("btn-outline-secondary");
            }
        });
    });
    </script>
</body>

</html>