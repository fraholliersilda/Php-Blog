<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT PROFILE</title>
    <link rel="stylesheet" href="/ATIS/css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
<?php include BASE_PATH . '/navbar/navbar.php'; ?>
<?php include BASE_PATH . '/actions/display_errors.php'; ?>

        <div class="prov">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="<?php echo htmlspecialchars($profilePicture['path']); ?>" alt="Admin"
                                    class="rounded-circle" width="150">
                                <h4><?php echo $user['username']; ?></h4>
                                <p class="text-secondary mb-1">Full Stack Developer</p>
                                <p class="text-muted font-size-sm">Tirane, Albania</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <!-- Username and Email Form -->
                <div class="card">
                    <div class="card-body">
                        <form action="/ATIS/views/profile/edit" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" value="updateUsername">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Username</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" class="form-control" name="username"
                                        value="<?php echo htmlspecialchars($user['username']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="email" class="form-control" name="email"
                                        value="<?php echo htmlspecialchars($user['email']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Save Username And Email"
                                        style="background-color:#1abc9c; color: white; border-color: #1abc9c;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Form -->
                <div class="card mt-4">
                    <div class="card-body">
                        <form action="/ATIS/views/profile/edit" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" value="updatePassword">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Old Password</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="password" class="form-control" name="old_password">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">New Password</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Save Password"
                                        style="background-color:#1abc9c; color: white; border-color: #1abc9c;">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Profile Picture Form -->
                <div class="card mt-4">
                    <div class="card-body">
                        <form action="/ATIS/views/profile/edit" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="action" value="updateProfilePicture">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Profile Picture</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="file" class="form-control" name="profile_picture">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Save Profile Picture"
                                        style="background-color:#1abc9c; color: white; border-color: #1abc9c;">
                                    <input type="button" class="btn btn-secondary px-4" value="Cancel"
                                        onclick="handleCancel(event)">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>



    <script src="../../js/script.js"></script>  
    <script src="../../js/edit_profile.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>