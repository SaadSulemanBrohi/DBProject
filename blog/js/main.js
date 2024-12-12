const navItems = document.queryselection('.nav__items');
const openNavBtn = document.queryselection('#open__nav-btn');
const closeNavBtn = document.queryselection('#close__nav-btn');

const openNav = () => {
    navItems.style.display = 'flex';
    openNavBtn.style.display ='none';
    closeNavBtn.style.display = 'inline-block';
}

const closeNav = () => {
    navItems.style.display = 'none';
    openNavBtn.style.display ='inline-block';
    closeNavBtn.style.display = 'none';
}

openNavBtn.addEventListener('click',openNav);
closeNavBtn.addEventListener('click',closeNav);