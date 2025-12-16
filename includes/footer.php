    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ArticleHub</h3>
                    <p>Une plateforme de partage d'articles et de connaissances.</p>
                    <p>Connecté à Oracle Database</p>
                </div>
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Accueil</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="create_article.php">Nouvel article</a></li>
                        <?php endif; ?>
                        <li><a href="#categories">Catégories</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Statistiques</h3>
                    <ul class="footer-links">
                        <?php
                        $stats = getStats();
                        echo "<li>Articles: " . $stats['articles'] . "</li>";
                        echo "<li>Utilisateurs: " . $stats['users'] . "</li>";
                        echo "<li>Commentaires: " . $stats['comments'] . "</li>";
                        ?>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 ArticleHub. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Script pour les fonctionnalités JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation active
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-links a');
            
            navLinks.forEach(link => {
                const linkPage = link.getAttribute('href').split('/').pop();
                if (linkPage === currentPage) {
                    link.style.color = 'var(--primary)';
                    link.style.fontWeight = 'bold';
                }
            });
            
            // Confirmation de suppression
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>