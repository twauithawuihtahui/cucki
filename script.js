// =========================
// Thêm vào giỏ hàng
// =========================
function addCart(itemType, itemId, quantity = 1){
    let payload = {};
    quantity = parseInt(quantity, 10);
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
    }

    if (arguments.length === 1) {
        payload.ma_cookie = itemType;
    } else {
        if (itemType === 'cookie') {
            payload.ma_cookie = itemId;
        } else if (itemType === 'combo') {
            payload.ma_combo = itemId;
        } else if (itemType === 'hop' || itemType === 'box') {
            payload.ma_hop = itemId;
        } else if (itemType === 'topping') {
            payload.ma_topping = itemId;
        } else {
            payload.ma_cookie = itemId;
        }
    }

    payload.quantity = quantity;
    const body = new URLSearchParams(payload).toString();

    fetch("add_cart.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body: body
    })
    .then(res=>res.text())
    .then(data=>{
        // Dùng trim() để loại bỏ các khoảng trắng thừa có thể có từ PHP
        let result = data.trim(); 

        if (result === "login") {
            alert("Vui lòng đăng nhập trước khi thêm vào giỏ hàng!");
            window.location.href = "login.php"; 
        } 
        else if (result === "success") {
            loadCart(); 
            alert("Đã thêm sản phẩm vào giỏ hàng thành công!");
        } 
        else {
            alert("Có lỗi xảy ra: " + result);
            console.log("Lỗi từ server:", result);
        }
    })
    .catch(err=>{
        alert("Lỗi kết nối: " + err);
        console.log(err);
    });
}

function changeCartQty(itemType, itemId, delta, currentQty, maxStock) {
    const newQty = currentQty + delta;
    if (newQty < 1) {
        updateCartQty(itemType, itemId, 0);
        return;
    }
    if (newQty > maxStock) {
        alert('Số lượng không thể vượt quá tồn kho');
        return;
    }
    updateCartQty(itemType, itemId, newQty);
}

function onCartQtyInput(input, itemType, itemId, maxStock) {
    let newQty = parseInt(input.value, 10);
    if (isNaN(newQty) || newQty < 1) {
        newQty = 1;
    }
    if (newQty > maxStock) {
        newQty = maxStock;
        alert('Số lượng không thể lớn hơn tồn kho');
    }
    input.value = newQty;
    updateCartQty(itemType, itemId, newQty);
}

function updateCartQty(itemType, itemId, quantity) {
    const payload = new URLSearchParams({
        type: itemType,
        id: itemId,
        quantity: quantity
    }).toString();

    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: payload
    })
    .then(res => res.text())
    .then(data => {
        const result = data.trim();
        if (result === 'login') {
            alert('Vui lòng đăng nhập để cập nhật giỏ hàng!');
            window.location.href = 'login.php';
            return;
        }
        if (result === 'success' || result === 'removed') {
            loadCart();
            return;
        }
        alert('Không thể cập nhật số lượng: ' + result);
        console.log('Update cart error:', result);
    })
    .catch(err => {
        alert('Lỗi kết nối: ' + err);
        console.error(err);
    });
}


// =========================
// Load giỏ hàng
// =========================
function loadCart(){

    fetch("load_cart.php")

    .then(res=>res.json())

    .then(data=>{
console.log(data);
        let cartItems=document.getElementById("cartItems");
        let cartCount=document.getElementById("cartCount");
        let cartTotal=document.getElementById("cartTotal");

        if(!cartItems) return;

        cartItems.innerHTML="";

        let total=0;
        let count=0;

       if(data.length==0){
 
           cartItems.innerHTML="Cart empty";
 
           cartCount.innerHTML=0;
           cartTotal.innerHTML=0;
 
           return;
 
       }
 
       data.forEach(item=>{
  
           const quantity = parseInt(item.so_luong, 10);
           const unitPrice = parseInt(item.gia.replace(/\./g, ""), 10) || 0;
           const itemTotal = unitPrice * quantity;

           count += quantity;
           total += itemTotal;
  
           cartItems.innerHTML += `
           <div class="cart-item">
               <img src="img/${item.hinh_anh}" width="60">
               <div class="cart-item-info">
                   <b>${item.ten_sp}</b>
                   <div class="cart-item-subtotal">${itemTotal.toLocaleString()} VNĐ</div>
                   <div class="cart-item-controls">
                       <button class="cart-qty-btn" onclick="changeCartQty('${item.type}', ${item.id}, -1, ${quantity}, ${parseInt(item.stock, 10)})">−</button>
                       <input
                           class="cart-qty-input"
                           type="number"
                           min="1"
                           max="${parseInt(item.stock, 10)}"
                           value="${quantity}"
                           onchange="onCartQtyInput(this, '${item.type}', ${item.id}, ${parseInt(item.stock, 10)})"
                           onblur="onCartQtyInput(this, '${item.type}', ${item.id}, ${parseInt(item.stock, 10)})"
                       />
                       <button class="cart-qty-btn cart-qty-plus" onclick="changeCartQty('${item.type}', ${item.id}, 1, ${quantity}, ${parseInt(item.stock, 10)})" ${quantity >= parseInt(item.stock, 10) ? 'disabled' : ''}>+</button>
                   </div>
               </div>
           </div>
           `;
  
       });

        cartCount.innerHTML=count;

        cartTotal.innerHTML=total.toLocaleString() + ' VND';

    });

}



// =========================
// Checkout
// =========================
function goCheckout(){

    if(!isLoggedIn){

        alert("Please login first!");

        window.location="login.php";

        return;

    }

    window.location="checkout.php";

}



// =========================
// Cuộn xuống sản phẩm
// =========================
function scrollToProducts(){

    let target=document.getElementById("products");

    if(target){

        target.scrollIntoView({

            behavior:"smooth"

        });

    }

}



// =========================
// Search
// =========================
function filterProducts(){

    const input = document.querySelector(".search-input");
    if (!input) {
        return;
    }

    let keyword = input.value.trim().toLowerCase();
    const cards = document.querySelectorAll(".cookie-card");
    if (!cards.length) {
        return;
    }

    cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        card.style.display = keyword === "" || text.includes(keyword) ? "block" : "none";
    });
}

let searchDebounceTimer = null;
let originalGridHtml = null;

function saveOriginalGrid() {
    const grid = document.querySelector('.cookie-grid');
    if (grid && originalGridHtml === null) {
        originalGridHtml = grid.innerHTML;
    }
}

function restoreOriginalGrid() {
    const grid = document.querySelector('.cookie-grid');
    if (!grid || originalGridHtml === null) {
        return;
    }
    grid.innerHTML = originalGridHtml;
}

async function fetchSearchResults(query) {
    const response = await fetch('search_api.php?q=' + encodeURIComponent(query));
    if (!response.ok) {
        return [];
    }
    const data = await response.json();
    return data;
}

function renderSearchResults(results) {
    const grid = document.querySelector('.cookie-grid');
    if (!grid) {
        return;
    }

    if (results.length === 0) {
        grid.innerHTML = '<div class="search-message">No products found.</div>';
        return;
    }

    grid.innerHTML = results.map(item => {
        return `
            <div class="cookie-card">
                <img src="img/${item.hinh_anh}">
                <h2>${item.ten_sp}</h2>
                <b>${item.gia} VND</b>
                <p class="stock-text">Tồn kho: ${parseInt(item.stock, 10) || 0}</p>
                <div class="button-group">
                    <button onclick="addCart('${item.type}', ${item.id})">Add To Cart</button>
                </div>
            </div>
        `;
    }).join('');
}

function updateSearchUrl(query) {
    const url = new URL(window.location.href);
    if (query) {
        url.searchParams.set('q', query);
    } else {
        url.searchParams.delete('q');
    }
    window.history.replaceState(null, '', url.toString());
}

function autoSearch(query) {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(async () => {
        if (query === '') {
            if (isSearchPage()) {
                window.location.href = 'index.php';
            } else {
                restoreOriginalGrid();
                updateSearchUrl('');
            }
            return;
        }

        const results = await fetchSearchResults(query);
        renderSearchResults(results);
        updateSearchUrl(query);
    }, 500);
}

function searchProducts(){

    const input = document.querySelector(".search-input");
    if (!input) {
        return;
    }

    let keyword = input.value.trim();
    if (!keyword) {
        if (isSearchPage()) {
            window.location.href = 'index.php';
        } else {
            restoreOriginalGrid();
            updateSearchUrl('');
        }
        return;
    }

    autoSearch(keyword);
}

// =========================
// Khi mở trang
// =========================
document.addEventListener("DOMContentLoaded",()=>{
    loadCart();
    saveOriginalGrid();

    let cartBtn=document.getElementById("cartBtn");

    if(cartBtn){

        cartBtn.onclick=function(){

            if(!isLoggedIn){

                alert("Please login first!");

                window.location="login.php";

                return;

            }

            document.getElementById("cartMenu").classList.toggle("show");

        }

    }

    document.querySelectorAll('.search-btn').forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            searchProducts();
        });
    });

    document.querySelectorAll('.search-input').forEach(input => {
        input.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchProducts();
            }
        });
        input.addEventListener('input', event => {
            clearTimeout(searchDebounceTimer);
            const value = event.target.value.trim();
            if (value === '') {
                if (window.location.pathname.endsWith('search.php')) {
                    window.location.href = 'index.php';
                } else {
                    restoreOriginalGrid();
                    updateSearchUrl('');
                }
                return;
            }
            autoSearch(value);
        });
    });

});