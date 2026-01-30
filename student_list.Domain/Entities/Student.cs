using System;

namespace student_list.Domain.Entities
{
    public class Student
    {
        // Primary key
        public int Id { get; set; }

        public string FirstName { get; set; } = string.Empty;
        public string LastName { get; set; } = string.Empty;
        // Gender as string per request
        public string Gender { get; set; } = string.Empty;
        public DateTime BirthDate { get; set; }
        public string? Address { get; set; }

        // Parameterless ctor for ORMs / deserialization
        protected Student() { }

        public Student(string firstName, string lastName, string gender, DateTime birthDate, string? address = null)
        {
            if (string.IsNullOrWhiteSpace(firstName)) throw new ArgumentException("First name is required.", nameof(firstName));
            if (string.IsNullOrWhiteSpace(lastName)) throw new ArgumentException("Last name is required.", nameof(lastName));
            if (string.IsNullOrWhiteSpace(gender)) throw new ArgumentException("Gender is required.", nameof(gender));
            if (birthDate == DateTime.MinValue) throw new ArgumentException("Birth date is required.", nameof(birthDate));

            FirstName = firstName.Trim();
            LastName = lastName.Trim();
            Gender = gender.Trim();
            BirthDate = birthDate.Date;
            Address = string.IsNullOrWhiteSpace(address) ? null : address.Trim();
        }

        public int Age
        {
            get
            {
                var today = DateTime.Today;
                var age = today.Year - BirthDate.Year;
                if (BirthDate.Date > today.AddYears(-age)) age--;
                return age;
            }
        }

        public void Update(string firstName, string lastName, string gender, DateTime birthDate, string? address = null)
        {
            if (string.IsNullOrWhiteSpace(firstName)) throw new ArgumentException("First name is required.", nameof(firstName));
            if (string.IsNullOrWhiteSpace(lastName)) throw new ArgumentException("Last name is required.", nameof(lastName));
            if (string.IsNullOrWhiteSpace(gender)) throw new ArgumentException("Gender is required.", nameof(gender));
            if (birthDate == DateTime.MinValue) throw new ArgumentException("Birth date is required.", nameof(birthDate));

            FirstName = firstName.Trim();
            LastName = lastName.Trim();
            Gender = gender.Trim();
            BirthDate = birthDate.Date;
            Address = string.IsNullOrWhiteSpace(address) ? null : address.Trim();
        }
    }
}
