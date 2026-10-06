        </section>
    </main>
</div>
<script>
const sidebar = document.getElementById('sidebar');
const toggle = document.getElementById('sidebarToggle');
toggle?.addEventListener('click', e => { e.stopPropagation(); sidebar.classList.toggle('open'); });
document.addEventListener('click', e => { if (!sidebar.contains(e.target)) sidebar.classList.remove('open'); });
</script>
</body>
</html>
