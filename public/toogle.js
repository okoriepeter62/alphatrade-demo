function toggleMenu() {
    let sidebar = document.getElementById("sidebar");

    if (sidebar.style.width === "60px") {
        sidebar.style.width = "250px";
    } else {
        sidebar.style.width = "60px";
    }
}