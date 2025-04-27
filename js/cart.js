function removeItem(id) {
    if (confirm('Are you sure you want to remove this item?')) {
        window.location.href = 'remove_from_cart.php?id=' + id;
    }
}
