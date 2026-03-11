using System;

namespace student_list.Domain.Models
{
    public class StudentModel
    {
        public int Id { get; set; }
        public string FirstName { get; set; } = string.Empty;
        public string LastName { get; set; } = string.Empty;
        // keep gender as string to match DB
        public string Gender { get; set; } = string.Empty;
        public DateTime BirthDate { get; set; }
        public string? Address { get; set; }
        public string FullName { get; set; } = string.Empty;
        public int Age { get; set; }
    }
}
