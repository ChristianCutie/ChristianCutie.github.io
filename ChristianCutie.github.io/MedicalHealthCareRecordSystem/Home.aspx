<%@ Page Title="" Language="C#" MasterPageFile="~/LandingPageHeader.master" AutoEventWireup="true" CodeFile="Home.aspx.cs" Inherits="Default2" %>

<asp:Content ID="Content1" ContentPlaceHolderID="ContentPlaceHolder1" Runat="Server">
       <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <div class="bg-dashboard-img">
        <div class="container">
            <div style="padding-top: 100px" class="row">
                <div class="col-lg-6">
                     
                </div>
                <div class="col-lg-6">
                   <h1 style="font-size: 64px" class=" text-end fw-bold font-blue">YOU AND OUR <span class="text-dark">DOCTOR</span></h1>
                    <p class="text-dark text-end">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ulla. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>
                        <a class="btn btn-danger btn-lg mx-auto me-4 float-end w-25" href="login.aspx">LOG IN</a>
                </div>
            </div>
        </div>
    </div>
    <section class="pt-5  pb-5">
        <div class="row mb-5">
                <div style="background-color:#48CAE4" class="col-lg-6 pt-3 pb-3">
        <div class="container">
                    <div class="d-flex align-items-center"></div>
                    <h3 class="fw-bold text-white">ABOUT US</h3>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row mb-5 pb-3">
                <div class="col-lg-6">
                    <div class="card shadow-none mt-5 bg-transparent border-0">
                        <div class="card-header bg-transparent border-0">
                            <h3 class="card-title font-blue">Our Mission</h3>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, 
                                 sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat
                                 volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ulla. Eodem modo typi,
                                 qui nunc nobis videntur parum clari, fiant sollemnes in futurum.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img class="img-thumbnail" src="image/our_mission.jpeg" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <img class="img-thumbnail" src="image/our_vission.jpeg" />
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-none mt-5 bg-transparent border-0">
                        <div class="card-header bg-transparent border-0">
                            <h3 class="card-title font-blue">Our Vision</h3>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, 
                                 sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat
                                 volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ulla. Eodem modo typi,
                                 qui nunc nobis videntur parum clari, fiant sollemnes in futurum.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section  style="background-color: #48CAE4" class="pt-5 pb-5">
        <div class="row">
            <div class="col-lg-6 pt-3 pb-3 bg-white">
                <div class="container">
                    <div class="d-flex align-items-center"></div>
                    <h3 class="fw-bold " style="color:#48CAE4 ">OUR DOCTORS</h3>
                </div>
            </div>
        </div>
        <div class="container">
            <h4 class="mt-5  text-uppercase">What we stand for</h4>
            <hr style="border: 3px solid #f8f8f8; width: 50%; margin-bottom: 3rem" />
            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #48CAE4" class=" fa fa-user-doctor fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">DOCTOR YOU CHOOSE</p>
                                    <p class="card-text"><small class="text-muted">Donec hendrerit rutrum nibh, id egestas magna sodales a. Fusce feugiat velit eu ante blandit facilisis.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-heart-pulse fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">YOUR HEALTHCARE</p>
                                    <p class="card-text"><small class="text-muted">Vestibulum imperdiet vestibulum laoreet. Integer elementum euismod ante sit amet elementum.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-hand-holding-heart fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">ALWAYS THERE FOR YOU</p>
                                    <p class="card-text"><small class="text-muted">Mauris commodo lacinia nisi a fermentum. Donec risus magna, fringilla laoreet ullamcorper in, lobortis semper enim.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-user-nurse fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">NURSING STAFF</p>
                                    <p class="card-text"><small class="text-muted">Donec hendrerit rutrum nibh, id egestas magna sodales a. Fusce feugiat velit eu ante blandit facilisis.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-phone-volume fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">EMERGENCY SERVICES</p>
                                    <p class="card-text"><small class="text-muted">Vestibulum imperdiet vestibulum laoreet. Integer elementum euismod ante sit amet elementum.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animation-zoom mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-building-circle-check fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">PREMIUM FACILITIES</p>
                                    <p class="card-text"><small class="text-muted">Mauris commodo lacinia nisi a fermentum. Donec risus magna, fringilla laoreet ullamcorper in, lobortis semper enim.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-5">
        <div class="row">
            <div style="background-color: #48CAE4" class="col-lg-6 pt-3 pb-3">
                <div class="container">
                    <div class="d-flex align-items-center"></div>
                    <h3 class="fw-bold text-white">OUR SERVICES</h3>
                </div>
            </div>
        </div>
        <div class="container">
            <h4 class="mt-5  text-uppercase">What we stand for</h4>
            <hr style="border: 3px solid #48CAE4; width: 50%; margin-bottom: 3rem" />
            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-user-doctor fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">DOCTOR YOU CHOOSE</p>
                                    <p class="card-text"><small class="text-muted">Donec hendrerit rutrum nibh, id egestas magna sodales a. Fusce feugiat velit eu ante blandit facilisis.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-heart-pulse fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">YOUR HEALTHCARE</p>
                                    <p class="card-text"><small class="text-muted">Vestibulum imperdiet vestibulum laoreet. Integer elementum euismod ante sit amet elementum.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-hand-holding-heart fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">ALWAYS THERE FOR YOU</p>
                                    <p class="card-text"><small class="text-muted">Mauris commodo lacinia nisi a fermentum. Donec risus magna, fringilla laoreet ullamcorper in, lobortis semper enim.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-user-nurse fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">NURSING STAFF</p>
                                    <p class="card-text"><small class="text-muted">Donec hendrerit rutrum nibh, id egestas magna sodales a. Fusce feugiat velit eu ante blandit facilisis.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-phone-volume fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">EMERGENCY SERVICES</p>
                                    <p class="card-text"><small class="text-muted">Vestibulum imperdiet vestibulum laoreet. Integer elementum euismod ante sit amet elementum.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3 h-100">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <i style="color: #00B4D8" class="fa fa-building-circle-check fs-1 mt-3 ms-3"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <p class="card-text mb-0 text-dark fw-bold">PREMIUM FACILITIES</p>
                                    <p class="card-text"><small class="text-muted">Mauris commodo lacinia nisi a fermentum. Donec risus magna, fringilla laoreet ullamcorper in, lobortis semper enim.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</asp:Content>

