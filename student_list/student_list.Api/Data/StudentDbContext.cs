using Microsoft.EntityFrameworkCore;
using student_list.Domain.Entities;

namespace student_list.Api.Data
{
    public class StudentDbContext : DbContext
    {
        public StudentDbContext(DbContextOptions<StudentDbContext> options) : base(options)
        {
        }

        public DbSet<Student> Students { get; set; } = null!;

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            modelBuilder.Entity<Student>(entity =>
            {
                entity.ToTable("Student");
                entity.HasKey(e => e.Id);

                entity.Property(e => e.Id).HasColumnName("Id");
                entity.Property(e => e.FirstName).HasColumnName("first_name").IsRequired();
                entity.Property(e => e.LastName).HasColumnName("last_name").IsRequired();
                entity.Property(e => e.Gender).HasColumnName("gender").IsRequired();
                entity.Property(e => e.BirthDate).HasColumnName("birth_date").IsRequired();
                entity.Property(e => e.Address).HasColumnName("address");
            });
        }
    }
}
