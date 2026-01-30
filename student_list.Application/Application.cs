using AutoMapper;
using student_list.Domain.Entities;
using student_list.Domain.Models;

namespace student_list.Application
{
    public class Application
    {

    }

    public class StudentProfile : Profile
    {
        public StudentProfile()
        {
            CreateMap<Student, StudentModel>().ReverseMap();
        }
    }
}
